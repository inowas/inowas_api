<?php

namespace Inowas\ScenarioAnalysisBundle\Command;

use AppBundle\Entity\ModFlowModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScenarioListCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:scenario:list')
            ->setDescription('Returns a list of all scenarios')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Show all Modflow-Scenarios with ID.");

        $sm = $this->getContainer()->get('inowas.scenarioanalysis.scenariomanager');
        $scenarios = $sm->findAll();

        $counter = 0;
        /** @var ModFlowModel $model */
        foreach ($scenarios as $scenario) {
            $output->writeln(sprintf("#%s, ID: %s, Name: %s", ++$counter, $scenario->getId()->toString(), $scenario->getName()));
        }
    }
}
