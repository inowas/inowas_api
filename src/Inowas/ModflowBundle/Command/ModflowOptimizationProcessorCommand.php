<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationFinder;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationStartRequest;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationStopRequest;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
use Inowas\ModflowModel\Model\Command\UpdateOptimizationCalculationState;
use Inowas\ModflowModel\Service\AMQPBasicProducer;
use Inowas\ModflowModel\Service\ModflowPackagesManager;
use Prooph\ServiceBus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowOptimizationProcessorCommand extends ContainerAwareCommand
{
    /** @var  OptimizationFinder */
    private $optimizationFinder;

    /** @var  AMQPBasicProducer */
    private $producer;

    /** @var ModflowPackagesManager */
    private $packagesManager;

    /** @var CommandBus */
    private $commandBus;

    protected function configure(): void
    {
        $this
            ->setName('inowas:optimization:processor')
            ->setDescription('Processes all relevant operations.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->optimizationFinder = $this->getContainer()->get('inowas.modflowmodel.optimization_finder');
        $this->commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $this->packagesManager = $this->getContainer()->get('inowas.modflowmodel.modflow_packages_manager');
        $this->producer = $this->getContainer()->get('inowas.modflowmodel.amqp_modflow_optimization');


        while (true) {
            // Get next row in queue
            $row = $this->optimizationFinder->getNextOptimizationToCalculate();

            if (\is_array($row)) {
                $modelId = ModflowId::fromString($row['model_id']);
                $optimizationId = ModflowId::fromString($row['optimization_id']);
                $this->calculateOptimization($modelId, $optimizationId, $output);
            }

            $row = $this->optimizationFinder->getNextOptimizationToCancel();

            if (\is_array($row)) {
                $modelId = ModflowId::fromString($row['model_id']);
                $optimizationId = ModflowId::fromString($row['optimization_id']);
                $this->calculateOptimization($modelId, $optimizationId, $output);
            }

            sleep(1);
        }
    }

    protected function calculateOptimization(ModflowId $modelId, ModflowId $optimizationId, OutputInterface $output): void
    {
        $output->writeln('Got a new Calculate-Optimization-Task:');
        $output->writeln('Model-Id: ' . $modelId->toString());
        $output->writeln('Optimization-Id: ' . $optimizationId->toString());

        $currentCalculationId = $this->packagesManager->getCalculationId($modelId);
        $output->writeln(sprintf('The current Calculation id is %s', $currentCalculationId->toString()));

        // Preprocessing
        $output->writeln('Start Preprocessing...');
        $this->commandBus->dispatch(UpdateOptimizationCalculationState::isPreprocessing($optimizationId));

        try {
            $newCalculationId = $this->packagesManager->recalculate($modelId);
        } catch (\exception $e) {
            $this->commandBus->dispatch(UpdateOptimizationCalculationState::errorRecalculatingModel($optimizationId));
            return;
        }

        // Preprocessing finished
        $output->writeln('Preprocessing finished, the new calculationId is: ' . $newCalculationId->toString());
        $this->commandBus->dispatch(UpdateOptimizationCalculationState::preprocessingFinished($optimizationId, $newCalculationId));

        if ($currentCalculationId->toString() !== $newCalculationId->toString()) {
            $this->commandBus->dispatch(CalculateModflowModel::forModflowModelFromTerminal($modelId));
        }

        try {
            $this->producer->publish(ModflowOptimizationStartRequest::startOptimization(
                $modelId,
                $this->packagesManager->getPackages($newCalculationId),
                $this->optimizationFinder->getOptimization($modelId)->input()
            ));
        } catch (\Exception $e) {
            $output->writeln('There was an exception thrown, trying to start an optimization.');
            $output->writeln(sprintf('Model-Id: %s', $modelId->toString()));
            $output->writeln(sprintf('Optimization-Id: %s', $this->optimizationFinder->getOptimization($modelId)->input()->optimizationId()->toString()));
            $output->writeln(sprintf('Error-Message: %s', $e->getMessage()));
            $this->commandBus->dispatch(UpdateOptimizationCalculationState::errorPublishing($optimizationId));
        }

        $this->commandBus->dispatch(UpdateOptimizationCalculationState::calculating($optimizationId));
    }

    protected function cancelOptimization(ModflowId $modelId, ModflowId $optimizationId, OutputInterface $output): void
    {
        $output->writeln('Got a new Cancel-Optimization-Task:');
        $output->writeln('Model-Id: ' . $modelId->toString());
        $output->writeln('Optimization-Id: ' . $optimizationId->toString());

        try {
            $this->producer->publish(ModflowOptimizationStopRequest::stopOptimization($modelId, $optimizationId));
        } catch (\Exception $e) {
            $output->writeln('There was an exception thrown, trying to stop an optimization.');
            $output->writeln(sprintf('Model-Id: %s', $modelId->toString()));
            $output->writeln(sprintf('Optimization-Id: %s', $this->optimizationFinder->getOptimization($modelId)->input()->optimizationId()->toString()));
            $output->writeln(sprintf('Error-Message: %s', $e->getMessage()));
            $this->commandBus->dispatch(UpdateOptimizationCalculationState::errorPublishing($optimizationId));
        }

        $this->commandBus->dispatch(UpdateOptimizationCalculationState::cancelled($optimizationId));

    }
}
