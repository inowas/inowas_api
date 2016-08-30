<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationPropertiesFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The port on localhost where the api is running.',
                80
            )
            ->addOption(
                'totim',
                null,
                InputOption::VALUE_OPTIONAL,
                'Show the list of heads available with total times',
                true
            )
            ->addOption(
                'submit',
                's',
                InputOption::VALUE_OPTIONAL,
                'Upload the data to local database, setting totim.',
                true
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

        $fpc = FlopyCalculationPropertiesFactory::loadFromApiAndRun($model);

        $apiBaseUrl = $this->getContainer()->getParameter('inowas.api_base_url');
        if ($input->getOption('port')){
            $apiBaseUrl = sprintf("http://localhost:%s/api", $input->getOption('port'));
        }

        if ($input->getOption('submit')){
            $fpc->setSubmit(true);
            $fpc->setTotim(null);

            if ($input->getOption('submit') != 'all'){
                $fpc->setTotim($input->getOption('submit'));
            }
        }

        $model->setCalculationProperties($fpc);
        $model->setHeads(array());
        $this->getContainer()->get('doctrine.orm.default_entity_manager')->persist($model);
        $this->getContainer()->get('doctrine.orm.default_entity_manager')->flush();

        $process = $flopy->calculate(
            $apiBaseUrl,
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