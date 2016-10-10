<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModflowCalculation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlopyQueueListCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:flopy:queue:list')
            ->setDescription('List all waiting Models in the queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("List all models in the Modflow queue."));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $calculations = $em->getRepository('AppBundle:ModflowCalculation')
            ->findBy(
                array('state' => 0),
                array('dateTimeAddToQueue' => 'ASC')
                );

        /** @var ModflowCalculation $calculation */
        foreach ($calculations as $calculation){
            $output->writeln($calculation->getModelId()->toString());
        }

        $output->writeln('List End.');
    }
}
