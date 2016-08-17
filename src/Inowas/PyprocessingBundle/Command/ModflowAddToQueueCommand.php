<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Service\Flopy;
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

        $model = $this->getContainer()->get('doctrine.orm.default_entity_manager')
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $input->getArgument('id')
            ));

        if (! $model instanceof ModFlowModel){
            $output->writeln(sprintf("The given id %s is no Model-Id", $input->getArgument('id')));
        }



        /** @var Flopy $flopy */
        $flopy = $this->getContainer()->get('inowas.flopy');

        $flopy->addToQueue(
            $this->getContainer()->getParameter('inowas.api_base_url'),
            $this->getContainer()->getParameter('inowas.modflow.data_folder'),
            $input->getArgument('id'),
            $model->getOwner()->getId()->toString()
        );
        $output->writeln('Modflow-Model has been added successfully.');
    }
}