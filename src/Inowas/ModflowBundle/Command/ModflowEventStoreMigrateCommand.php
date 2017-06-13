<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\UserId;
use Inowas\ModflowBundle\DataFixtures\Scenarios\Hanoi\Hanoi;
use Inowas\ModflowBundle\DataFixtures\Scenarios\Hanoi\HanoiBaseModelOnly;
use Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero\RioPrimero;
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

        if (null === $modelname){
            $output->writeln('Possible Arguments are:');
            $output->writeln('1 or HanoiBaseModel for the Hanoi BaseModel');
            $output->writeln('2 or Hanoi for the hanoi-modflow-model');
            $output->writeln('3 or Rio Primero for the hanoi-modflow-model');
        }

        if ($modelname === 'Hanoi Basemodel only' || (int)$modelname === 1) {
            $hanoi = new HanoiBaseModelOnly();
            $hanoi->setContainer($this->getContainer());
            $hanoi->load();
        }

        if ($modelname === 'Hanoi Basemodel with Scenarios' || (int)$modelname === 2) {
            $hanoi = new Hanoi();
            $hanoi->setContainer($this->getContainer());
            $hanoi->load();
        }

        if ($modelname === 'Rio Primero' || (int)$modelname === 3) {
            $rioPrimero = new RioPrimero();
            $rioPrimero->setContainer($this->getContainer());
            $rioPrimero->load();
        }
    }
}
