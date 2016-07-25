<?php

namespace Inowas\PyprocessingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class ModflowProcessRunnerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:modflow:process:runner')
            ->setDescription('Service that runs all models in the queue.')
            ->addOption(
                'daemon',
                null,
                InputOption::VALUE_NONE,
                'If set, the process will run as daemon.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $lockHandler = new LockHandler('inowas.modflow.servicerunner');
        if (!$lockHandler->lock()) {
            $output->writeln('This command is already running in another process.');
            return 0;
        }

        if ($input->getOption('daemon') == true){
            $output->writeln(sprintf("Start ServiceRunner as Daemon"));
            $modflowServiceRunner = $this->getContainer()->get('inowas.modflow.servicerunner');
            $modflowServiceRunner->run(true);
        }

        $output->writeln(sprintf("Start ServiceRunner"));
        $modflowServiceRunner = $this->getContainer()->get('inowas.modflow.servicerunner');
        $modflowServiceRunner->run(false);
    }
}