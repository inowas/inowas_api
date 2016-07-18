<?php

namespace InowasPyprocessingBundle\Command;

use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowAddToQueueCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:modflow:queue:add')
            ->setDescription('Add Modflow Model to Calculation Queue')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The ModflowModel-Id is needed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Adding model id: %s to queue.", $input->getArgument('id')));

        if (! Uuid::isValid($input->getArgument('id'))){
            $output->writeln(sprintf("The given id: %s is not valid", $input->getArgument('id')));
        }

        /** @var EntityManager $entityManager */
        $modflow = $this->getContainer()->get('inowas.modflow');
        $modflow->addToQueue($input->getArgument('id'), 'mf2005');
        $output->writeln('Modflow-Model has been added successfully.');
    }
}