<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowCalculationCommand extends ContainerAwareCommand
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
            ->setName('inowas:calculation:calculate')
            ->setDescription('Calculates a model by modelId')
            ->addArgument('modelId', InputArgument::REQUIRED, 'The modelId to calculate')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $output->writeln(sprintf('Calculating Model with id: %s', $input->getArgument('modelId')));
        $commandBus->dispatch(CalculateModflowModel::forModflowModelFromTerminal(ModflowId::fromString($input->getArgument('modelId'))));
    }
}
