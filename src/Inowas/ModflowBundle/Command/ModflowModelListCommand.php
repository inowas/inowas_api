<?php

namespace Inowas\ModflowBundle\Command;

use AppBundle\Entity\ModFlowModel;
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

        $mm = $this->getContainer()->get('inowas.modflow.toolmanager');
        $models = $mm->findAllModels();

        $counter = 0;
        /** @var ModFlowModel $model */
        foreach ($models as $model) {
            $output->writeln(sprintf("#%s, ID: %s, Name: %s", ++$counter, $model->getId()->toString(), $model->getName()));
        }
    }
}
