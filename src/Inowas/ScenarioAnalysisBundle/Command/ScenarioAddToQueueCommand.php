<?php

namespace Inowas\ScenarioAnalysisBundle\Command;

use Inowas\ScenarioAnalysisBundle\Service\ScenarioManager;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScenarioAddToQueueCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:scenario:queue')
            ->setDescription('Add model to calculations queue')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The model id is needed.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Calculating scenario id: %s", $input->getArgument('id')));

        if (! Uuid::isValid($input->getArgument('id'))){
            $output->writeln(sprintf("The given id: %s is not valid", $input->getArgument('id')));
        }

        /** @var ScenarioManager $sm */
        $sm = $this->getContainer()->get('inowas.scenarioanalysis.scenariomanager');
        $scenario = $sm->findById($input->getArgument('id'));

        $cm = $this->getContainer()->get('inowas.modflow.calculationmanager');
        $calculation = $cm->createFromScenario($scenario);
        $cm->update($calculation);

        $output->writeln('Scenario has been added to queue.');
    }
}
