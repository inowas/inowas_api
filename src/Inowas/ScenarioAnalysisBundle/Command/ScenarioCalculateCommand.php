<?php

namespace Inowas\ScenarioAnalysisBundle\Command;

use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Calculation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

class ScenarioCalculateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:scenario:calculate')
            ->setDescription('Calculate ModflowModel by CalculationId or ScenarioId')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The CalculationId or ScenarioId'
            )
            ->addOption(
                'async',
                'a',
                InputOption::VALUE_OPTIONAL,
                'Start a asynchronous Job.',
                false
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $cm = $this->getContainer()->get('inowas.modflow.calculationmanager');
        $calculation = $cm->findById($id);

        if (! $calculation instanceof Calculation) {
            $calculation = $cm->findByModelId($id);
        }

        if (! $calculation instanceof Calculation) {
            throw new InvalidArgumentException(sprintf('There is no calculation with ID=%s', $id));
        }

        $flopy = $this->getContainer()->get('inowas.flopy');

        $output->writeln(sprintf("Calculating model id: %s", $calculation->getModelId()));
        $process = $flopy->calculate($calculation, true);
        $output->writeln($process->getCommandLine());
        $process->run();

        if ($process->isSuccessful()){
            $output->writeln('The Process has finished with success.');
            $output->writeln($process->getOutput());
            $calculation->setOutput($process->getOutput());
            $calculation->setFinishedWithSuccess(true);
        } else {
            $output->writeln('The Process has with errors.');
            $output->writeln($process->getErrorOutput());
            $calculation->setOutput($process->getErrorOutput());
            $calculation->setFinishedWithSuccess(false);
        }

        /**
         *
        if ($input->getOption('async') === 'true'){
            $flopy->addToQueue($apiBaseUrl, $dataFolder, $model->getId()->toString(), $model->getOwner()->getId()->toString());
            $flopy->startAsyncFlopyProcessRunner($this->getContainer()->get('kernel')->getRootDir());
            return 1;
        }
         */

        return 1;
    }
}
