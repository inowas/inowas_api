<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationPropertiesFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FlopyCalculateCommand extends FlopyCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:flopy:calculate')
            ->setDescription('Calculate Flopy Model')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'The ModflowModel-Id or Number in the List'
            )
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The port on localhost where the api is running.',
                80
            )
            ->addOption(
                'totim',
                null,
                InputOption::VALUE_OPTIONAL,
                'Show the list of heads available with total times',
                true
            )
            ->addOption(
                'submit',
                's',
                InputOption::VALUE_OPTIONAL,
                'Upload the data to local database, setting totim.',
                true
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

        $response = $this->getModelFromInput($input, $output);

        if ($response instanceof ModFlowModel){

            /** @var ModFlowModel $model */
            $model = $response;
            $output->writeln(sprintf("Calculating model id: %s", $input->getArgument('id')));

            $flopy = $this->getContainer()->get('inowas.flopy');
            $dataFolder = $this->getContainer()->getParameter('inowas.modflow.data_folder');

            $fpc = FlopyCalculationPropertiesFactory::loadFromApiRunAndSubmit($model);

            $apiBaseUrl = $this->getContainer()->getParameter('inowas.api_base_url');
            if ($input->getOption('port')){
                $apiBaseUrl = sprintf("http://localhost:%s/api", $input->getOption('port'));
            }

            if ($input->getOption('submit') === 'false'){
                $fpc->setSubmit(false);
            }

            $model->setCalculationProperties($fpc);
            $model->setHeads(array());
            $this->getContainer()->get('doctrine.orm.default_entity_manager')->persist($model);
            $this->getContainer()->get('doctrine.orm.default_entity_manager')->flush();

            if ($input->getOption('async') === 'true'){
                $flopy->addToQueue($apiBaseUrl, $dataFolder, $model->getId()->toString(), $model->getOwner()->getId()->toString());
                $flopy->startAsyncFlopyProcessRunner($this->getContainer()->get('kernel')->getRootDir());
                return 1;
            }

            $process = $flopy->calculate(
                $apiBaseUrl,
                $dataFolder,
                $model->getId()->toString(),
                $model->getOwner()->getApiKey(),
                true
            );

            $output->writeln($process->getProcess()->getCommandLine());
            $process->run();

            if ($process->isSuccessful()){
                $output->writeln('The Process has finished successful.');
                $output->writeln($process->getOutput());
            }

            $output->writeln($process->getErrorOutput());
        }
        return 1;
    }
}