<?php

namespace Inowas\PyprocessingBundle\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlopyCalculateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:flopy:calculate')
            ->setDescription('Calculate Flopy Model')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The ModflowModel-Id is needed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (! Uuid::isValid($input->getArgument('id'))){
            $output->writeln(sprintf("The given id: %s is not valid", $input->getArgument('id')));
            return;
        }

        $output->writeln(sprintf("Calculating model id: %s", $input->getArgument('id')));

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $flopy = $this->getContainer()->get('inowas.flopy');
        $dataFolder = $this->getContainer()->getParameter('inowas.modflow.data_folder');

        $model = $em->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array('id' => $input->getArgument('id')));

        $process = $flopy->calculate(
            'http://localhost/api',
            $dataFolder,
            $model->getId()->toString(),
            $model->getOwner()->getApiKey(),
            true
        );

        $output->writeln($process->getProcess()->getCommandLine());
        $process->run();

        if ($process->isSuccessful()){
            $output->writeln('The Process has finished successful.');
            $output->writeln($process->getOutput());
        }

        $output->writeln($process->getErrorOutput());
    }
}