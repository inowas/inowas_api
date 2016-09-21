<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Model\Modflow\Package\PackageFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlopyValidateCommand extends FlopyCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:flopy:validate')
            ->setDescription('Validate the flopy-packages')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'The ModflowModel-Id or Number in the List'
            )
            ->addArgument(
                'package',
                InputArgument::OPTIONAL,
                'The PackageName is required'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $response = $this->getModelFromInput($input, $output);

        if ($response instanceof ModFlowModel) {
            $model = $response;
            $package = PackageFactory::create($input->getArgument('package'), $model);
            $output->writeln(json_encode($package));
        }

        return 1;
    }
}