<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
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
                InputArgument::OPTIONAL,
                'The ModflowModel-Id or Number in the List'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (! $input->getArgument('id')){
            $modflowModels = $this->getContainer()->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:ModFlowModel')
                ->findBy(
                    array(),
                    array('dateCreated' => 'ASC')
                );

            $counter = 0;
            /** @var ModFlowModel $modflowModel */
            foreach ($modflowModels as $modflowModel) {
                $output->writeln(sprintf("#%s, ID: %s, Name: %s, Owner: %s ", ++$counter, $modflowModel->getId()->toString(), $modflowModel->getName(), $modflowModel->getOwner()));
            }

            return 1;
        }

        if (Uuid::isValid($input->getArgument('id'))){
            $model = $this->getContainer()->get('doctrine.orm.default_entity_manager')
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $input->getArgument('id')
                ));

            if (! $model instanceof ModFlowModel){
                $output->writeln(sprintf("The given id: %s is not a valid Model", $input->getArgument('id')));
                return 0;
            }


        } else {
            $modflowModels = $this->getContainer()->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:ModFlowModel')
                ->findBy(
                    array(),
                    array('dateCreated' => 'ASC')
                );

            if (count($modflowModels) < $input->getArgument('id')){
                $output->writeln(sprintf("The given id: %s is not valid", $input->getArgument('id')));
                return 0;
            }

            $model = $modflowModels[$input->getArgument('id')-1];
        }

        $output->writeln(sprintf("Calculating model id: %s", $input->getArgument('id')));

        $flopy = $this->getContainer()->get('inowas.flopy');
        $dataFolder = $this->getContainer()->getParameter('inowas.modflow.data_folder');


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

        return 1;
    }
}