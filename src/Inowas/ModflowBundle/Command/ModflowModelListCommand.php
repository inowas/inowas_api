<?php

namespace Inowas\ModflowBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowModelListCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:model:list')
            ->setDescription('Returns a list of all modflowmodels')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln("Show all Modflow-Models with ID.");

        $em = $this->getContainer()->get('doctrine')->getManager();
        $modflowModels = $em->getRepository('AppBundle:ModFlowModel')
            ->findAll();
        
        foreach ($modflowModels as $modflowModel) {
            $output->writeln(sprintf("ID: %s, Name: %s, Owner: %s ", $modflowModel->getId(), $modflowModel->getName(), $modflowModel->getOwner()));
        }
    }
}