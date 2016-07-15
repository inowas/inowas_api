<?php

namespace Inowas\ModflowBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowProcessQueueCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:modflow:queue:process')
            ->setDescription('Process all waiting Models.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Process all models in the Modflow queue."));
        $modflow = $this->getContainer()->get('inowas.modflow');
        $modflow->processQueue();
        $output->writeln('List End.');
    }
}