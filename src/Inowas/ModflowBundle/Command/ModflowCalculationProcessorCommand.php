<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Infrastructure\Projection\Calculation\CalculationProcessFinder;
use Inowas\ModflowModel\Model\AMQP\ModflowCalculationRequest;
use Inowas\ModflowModel\Model\Command\UpdateCalculationId;
use Inowas\ModflowModel\Model\Command\UpdateCalculationState;
use Inowas\ModflowModel\Service\AMQPModflowCalculation;
use Inowas\ModflowModel\Service\ModflowPackagesManager;
use Prooph\ServiceBus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowCalculationProcessorCommand extends ContainerAwareCommand
{

    /** @var  CalculationProcessFinder */
    private $calculationProcessFinder;

    /** @var  AMQPModflowCalculation */
    private $calculator;

    /** @var ModflowPackagesManager */
    private $packagesManager;

    /** @var CommandBus */
    private $commandBus;


    protected function configure(): void
    {
        $this
            ->setName('inowas:calculation:processor')
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
        $this->calculationProcessFinder = $this->getContainer()->get('inowas.modflowmodel.calculation_process_finder');
        $this->calculator = $this->getContainer()->get('inowas.modflowmodel.amqp_modflow_calculation');
        $this->commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $this->packagesManager = $this->getContainer()->get('inowas.modflowmodel.modflow_packages_manager');

        while (true) {
            // Get next row in queue
            $row = $this->calculationProcessFinder->getNextRowWithState(CalculationState::calculationProcessStarted());

            if (\count($row) === 0) {
                sleep(1);
                continue;
            }

            $modelId = ModflowId::fromString($row[0]['model_id']);
            $output->writeln('Got a new Task: '.$modelId->toString());

            // Preprocessing
            $output->writeln('Start Preprocessing...');
            $this->commandBus->dispatch(UpdateCalculationState::isPreprocessing($modelId));
            $output->writeln('Start Preprocessing Dispatched.');
            $calculationId = $this->packagesManager->recalculate($modelId);

            // Preprocessing finished
            $output->writeln('Preprocessing finished, the new calculationId is: '.$calculationId->toString());
            $this->commandBus->dispatch(UpdateCalculationState::preprocessingFinished($modelId, $calculationId));
            $this->commandBus->dispatch(UpdateCalculationId::withId($modelId, $calculationId));

            // Calculation
            $output->writeln('Send to calculation to calculation service.');
            $packages = $this->packagesManager->getPackages($calculationId);
            $request = ModflowCalculationRequest::fromParams($modelId, $calculationId, $packages);
            $this->calculator->calculate($request);
            $this->commandBus->dispatch(UpdateCalculationState::queued($modelId, $calculationId));
            unset($row);
        }
    }
}
