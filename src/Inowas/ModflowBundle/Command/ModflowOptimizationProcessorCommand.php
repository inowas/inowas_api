<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationFinder;
use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationProcessFinder;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationRequest;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
use Inowas\ModflowModel\Model\Command\UpdateCalculationId;
use Inowas\ModflowModel\Service\AMQPBasicProducer;
use Inowas\ModflowModel\Service\ModflowPackagesManager;
use Prooph\ServiceBus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowOptimizationProcessorCommand extends ContainerAwareCommand
{

    /** @var  OptimizationProcessFinder */
    private $optimizationProcessFinder;

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
        $this->optimizationProcessFinder = $this->getContainer()->get('inowas.modflowmodel.optimization_process_finder');
        $this->commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $this->packagesManager = $this->getContainer()->get('inowas.modflowmodel.modflow_packages_manager');
        $this->producer = $this->getContainer()->get('inowas.modflowmodel.amqp_modflow_optimization');


        while (true) {
            // Get next row in queue
            $row = $this->optimizationProcessFinder->getNextRowWithState(OptimizationState::started());

            if (\count($row) === 0) {
                sleep(1);
                continue;
            }

            $modelId = ModflowId::fromString($row[0]['model_id']);
            $output->writeln('Got a new Task: ' . $modelId->toString());

            $currentCalculationId = $this->packagesManager->getCalculationId($modelId);
            $output->writeln(sprintf('The current Calculation id is %s', $currentCalculationId->toString()));

            // Preprocessing
            $output->writeln('Start Preprocessing...');
            $newCalculationId = $this->packagesManager->recalculate($modelId);

            // Preprocessing finished
            $output->writeln('Preprocessing finished, the new calculationId is: ' . $newCalculationId->toString());

            if ($currentCalculationId->toString() !== $newCalculationId->toString()) {
                $this->commandBus->dispatch(UpdateCalculationId::withId($modelId, $newCalculationId));
                $this->commandBus->dispatch(CalculateModflowModel::forModflowModelFromTerminal($modelId));
            }

            try {
                $this->producer->publish(ModflowOptimizationRequest::fromParams(
                    $modelId,
                    $this->packagesManager->getPackages($newCalculationId),
                    $this->optimizationFinder->getOptimization($modelId)->input()
                ));
            } catch (\Exception $e) {
                $output->writeln('There was an exception thrown, trying to start an optimization.');
                $output->writeln(sprintf('Model-Id: %s', $modelId->toString()));
                $output->writeln(sprintf('Optimization-Id: %s', $this->optimizationFinder->getOptimization($modelId)->input()->optimizationId()->toString()));
                $output->writeln(sprintf('Error-Message: %s', $e->getMessage()));
                $this->optimizationProcessFinder->removeById($row[0]['id']);
            }
        }
    }
}
