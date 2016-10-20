<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowModelInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class FlopyCommand extends ContainerAwareCommand
{

    protected function getModelFromInput(InputInterface $input, OutputInterface $output){
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

            if (!$model instanceof ModflowModelInterface){
                $model = $this->getContainer()->get('doctrine.orm.default_entity_manager')
                    ->getRepository('AppBundle:ModflowModelScenario')
                    ->findOneBy(array(
                        'id' => $input->getArgument('id')
                    ));
            }

            if (! $model instanceof ModflowModelInterface){
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

        return $model;
    }
}
