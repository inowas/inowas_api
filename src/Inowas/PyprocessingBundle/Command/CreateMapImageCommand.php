<?php

namespace Inowas\PyprocessingBundle\Command;

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
            ->setDescription('Creates an image of the modflowmodel')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'The ModflowModel-Id or Number in the List'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $model = $this->getModelFromInput($input, $output);

        if ($model instanceof ModflowModelInterface) {
            $mapImage = $this->getContainer()->get('inowas.mapimage');
            $mapImage->createImage($model);
        }

        return 1;
    }
}
