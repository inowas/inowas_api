<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\ModflowModelScenario;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowModelInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMapImageCommand extends FlopyCommand
{
    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:model:image')
            ->setDescription('Creates an image for the modflowmodel')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'The ModflowModel-Id or Number in the List'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mapImage = $this->getContainer()->get('inowas.mapimage');

        /** @var ModflowModelInterface $model */
        $model = $this->getModelFromInput($input, $output);

        if ($model instanceof ModflowModelInterface) {
            $output->writeln(sprintf('Create Image for BaseModel id=%s', $model->getId()));
            $mapImage->createImage($model);
        }

        if ($model instanceof ModFlowModel){
            $scenarios = $this->getContainer()->get('doctrine.orm.default_entity_manager')
               ->getRepository('AppBundle:ModflowModelScenario')
               ->findBy(array(
                   'baseModel' => $model->getId()
               ));

            /** @var ModflowModelScenario $scenario */
            foreach ($scenarios as $scenario){
                $output->writeln(sprintf('Create Image for Scenario id=%s', $scenario->getId()));
                $mapImage->createImage($scenario);
            }
        }

        return 1;
    }
}
