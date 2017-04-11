<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\UserId;
use Inowas\ModflowBundle\DataFixtures\Scenarios\Hanoi\Hanoi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowEventStoreMigrateCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:es:migrate')
            ->setDescription('Migrates the Hanoi-Model to the Database')
            ->addArgument('model', InputArgument::OPTIONAL, 'The model which to load')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $modelname =  $input->getArgument('model');

        if (is_null($modelname)){
            $output->writeln("Possible Arguments are:");
            $output->writeln("1 or hanoi for the hanoi-modflow-model");
        }

        if ($modelname == 'hanoi' || intval($modelname) == 1) {
            $hanoi = new Hanoi();
            $hanoi->setContainer($this->getContainer());
            $hanoi->load();
        }
    }
}
