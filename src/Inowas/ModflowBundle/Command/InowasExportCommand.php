<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Boundaries\BoundaryCollection;
use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\ModflowModelExchangeDTO;
use Inowas\Common\Soilmodel\LayerCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InowasExportCommand extends ContainerAwareCommand
{
    /**
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('inowas:export')
            ->setDescription('Exports a modflowModel by id')
            ->addArgument('modelId', InputArgument::REQUIRED, 'The modelId to export')
            ->addOption('geometry', 'g', InputOption::VALUE_NONE, 'Export geometry only.')
            ->addOption('boundaries', 'b', InputOption::VALUE_NONE, 'Export boundaries only.')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'BoundaryType.')
        ;

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modelId = ModflowId::fromString($input->getArgument('modelId'));
        $output->writeln(sprintf('Loading Model with id: %s', $modelId->toString()));

        $userId = $this->getContainer()->get('inowas.modflowmodel.manager')->getUserId($modelId);

        if (!$userId instanceof UserId) {
            $output->writeln(sprintf('No model found with id %s.', $modelId->toString()));
            return;
        }

        $model = $this->getContainer()->get('inowas.modflowmodel.manager')->findModel($modelId, $userId);

        if ($input->getOption('geometry')) {
            $output->writeln($model->geometry()->toJson());
            return;
        }

        $layers = $this->getContainer()->get('inowas.modflowmodel.soilmodel_finder')->getLayers($modelId);

        $layersCollection = LayerCollection::create();
        foreach ($layers as $layer) {
            $layersCollection->addLayer($layer);
        }

        $boundaries = $this->getContainer()->get('inowas.modflowmodel.boundary_manager')->findBoundaries($modelId);
        $boundaryCollection = BoundaryCollection::create();
        foreach ($boundaries as $boundary) {
            $boundaryCollection->addBoundary($boundary);
        }

        if ($input->getOption('boundaries')) {
            if ($input->getOption('type')) {
                $output->writeln(
                    json_encode(
                        $boundaryCollection->filter(BoundaryType::fromString($input->getOption('type')))->toArray()
                    ));
                return;
            }

            $output->writeln(json_encode($boundaryCollection->toArray()));
            return;
        }

        $layers = $this->getContainer()->get('inowas.modflowmodel.soilmodel_finder')->getLayers($modelId);
        $layerCollection = LayerCollection::create();
        foreach ($layers as $layer) {
            $layerCollection->addLayer($layer);
        }

        $packages = $this->getContainer()->get('inowas.modflowmodel.modflow_packages_manager')->getPackagesByModelId($modelId);

        $modelDTO = ModflowModelExchangeDTO::fromParams(
            $model,
            $layerCollection,
            $boundaryCollection,
            $packages
        );

        $output->writeln(json_encode($modelDTO));
    }
}
