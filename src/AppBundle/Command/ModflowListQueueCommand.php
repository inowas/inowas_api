<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowListQueueCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:modflow:queue:list')
            ->setDescription('List all waiting Models in the queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("List all models in the Modflow queue."));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $models = $em->getRepository('AppBundle:ModflowCalculation')
            ->findBy(
                array('state' => 0),
                array('dateTimeAddToQueue' => 'ASC')
                );

        foreach ($models as $model){
            $output->writeln($model->getId()->toString());
        }

        $output->writeln('List End.');
    }
}