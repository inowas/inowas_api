<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModelListCommand extends ContainerAwareCommand
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
            ->findBy(
                array(),
                array('dateCreated' => 'ASC')
            );

        $counter = 0;
        /** @var ModFlowModel $modflowModel */
        foreach ($modflowModels as $modflowModel) {
            $output->writeln(sprintf("#%s, ID: %s, Name: %s, Owner: %s ", ++$counter, $modflowModel->getId()->toString(), $modflowModel->getName(), $modflowModel->getOwner()));
        }
    }
}
