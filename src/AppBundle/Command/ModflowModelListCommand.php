<?php

namespace AppBundle\Command;

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
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $modflowModels = $em->getRepository('AppBundle:ModFlowModel')
            ->findAll();

        foreach ($modflowModels as $modflowModel) {
            echo (sprintf("ID: %s, Name: %s, Owner: %s \r\n", $modflowModel->getId(), $modflowModel->getName(), $modflowModel->getOwner()));
        }
    }
}