<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowCalculationPackagesCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    /**
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('inowas:calculation:packages')
            ->setDescription('Recalculates all packages')
            ->addArgument('modelId', InputArgument::REQUIRED, 'The modelId to calculate')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Recalculating Packages for Model with id: %s', $input->getArgument('modelId')));
        $packageManager = $this->getContainer()->get('inowas.modflowmodel.modflow_packages_manager');
        $calculationId = $packageManager->recalculate(ModflowId::fromString($input->getArgument('modelId')));
        $output->writeln('Model successful recalculated. New calculationId is '.$calculationId->toString().'.');
    }
}
