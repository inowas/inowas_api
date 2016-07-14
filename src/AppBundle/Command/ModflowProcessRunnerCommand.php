<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowProcessRunnerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:modflow:process:runner')
            ->setDescription('Service that never stops and runs all services from queue.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Start Service"));
        $modflowServiceRunner = $this->getContainer()->get('inowas.modflow.service.runner');
        $modflowServiceRunner->run();
    }
}