<?php

namespace Inowas\ModflowBundle\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowCalculateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:model:queue')
            ->setDescription('Add model to queue')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The model id is needed.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Calculating model id: %s", $input->getArgument('id')));

        if (! Uuid::isValid($input->getArgument('id'))){
            $output->writeln(sprintf("The given id: %s is not valid", $input->getArgument('id')));
        }

        $mm = $this->getContainer()->get('inowas.modflow.modelmanager');
        $model = $mm->findById($input->getArgument('id'));

        $cm = $this->getContainer()->get('inowas.modflow.calculationmanager');
        $calculation = $cm->create($model);
        $calculation->setBaseUrl($this->getContainer()->getParameter('inowas.api_base_url'));
        $calculation->setDataFolder($this->getContainer()->getParameter('inowas.modflow.data_folder'));
        $cm->update($calculation);

        $output->writeln('Modflow-Model has been added to queue.');
    }
}
