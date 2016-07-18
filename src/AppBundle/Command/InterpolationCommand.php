<?php

namespace AppBundle\Command;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\PropertyType;
use Inowas\PyprocessingBundle\Service\Interpolation;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InterpolationCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:model:interpolate')
            ->setDescription('Interpolates all Layers automatically.')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The ModflowModel-Id is needed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Interpolating Layers"));

        try {
            $id = Uuid::fromString($input->getArgument('id'));
        } catch (\InvalidArgumentException $e) {
            $output->writeln(sprintf("The given id: %s is not valid", $input->getArgument('id')));
            return;
        }

        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $geoTools = $this->getContainer()->get('inowas.geotools');
        $soilModelService = $this->getContainer()->get('inowas.soilmodel');

        ;
        $soilModelService->loadModflowModelById($id);

        $modFlowModel = $soilModelService->getModflowModel();
        $modFlowModel->setActiveCells($geoTools->getActiveCells($modFlowModel->getArea(), $modFlowModel->getBoundingBox(), $modFlowModel->getGridSize()));
        $entityManager->persist($modFlowModel);
        $entityManager->flush();

        $layers = $modFlowModel->getSoilModel()->getGeologicalLayers();

        /** @var GeologicalLayer $layer */
        foreach ($layers as $layer)
        {
            $propertyTypes = $soilModelService->getAllPropertyTypesFromLayer($layer);
            /** @var PropertyType $propertyType */
            foreach ($propertyTypes as $propertyType){
                if ($propertyType->getAbbreviation() == PropertyType::TOP_ELEVATION && $layer->getOrder() != GeologicalLayer::TOP_LAYER) {
                    continue;
                }

                $output->writeln(sprintf("Interpolating Layer %s, Property %s", $layer->getName(), $propertyType->getName()));
                $output = $soilModelService->interpolateLayerByProperty(
                    $layer,
                    $propertyType->getAbbreviation(),
                    array(Interpolation::TYPE_IDW, Interpolation::TYPE_MEAN)
                );

                echo ($output);
            }
        }
    }
}