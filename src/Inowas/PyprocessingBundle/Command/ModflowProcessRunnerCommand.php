<?php

namespace Inowas\PyprocessingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class ModflowProcessRunnerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:modflow:process:runner')
            ->setDescription('Service that runs alls models in the queue.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $lockHandler = new LockHandler('inowas.modflow.servicerunner');
        if (!$lockHandler->lock()) {
            $output->writeln('This command is already running in another process.');
            return 0;
        }

        $output->writeln(sprintf("Start Service"));
        $modflowServiceRunner = $this->getContainer()->get('inowas.modflow.servicerunner');
        $modflowServiceRunner->run();
    }
}