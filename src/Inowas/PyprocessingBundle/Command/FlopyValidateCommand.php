<?php

namespace Inowas\PyprocessingBundle\Command;

use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Model\Modflow\Package\PackageFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlopyValidateCommand extends ContainerAwareCommand
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

        $package = PackageFactory::create($input->getArgument('package'), $model);
        $output->writeln(json_encode($package));
        return 1;
    }
}