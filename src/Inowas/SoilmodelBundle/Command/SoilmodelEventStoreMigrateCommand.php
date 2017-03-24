<?php

declare(strict_types=1);

namespace Inowas\SoilmodelBundle\Command;

use Inowas\Common\Id\UserId;
use Inowas\SoilmodelBundle\DataFixtures\HanoiSoilmodel\HanoiSoilModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SoilmodelEventStoreMigrateCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:soilmodel:es:migrate')
            ->setDescription('Migrates the Hanoi-Model to the Database')
            ->addArgument('model', InputArgument::REQUIRED, 'The model which to load')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('model') == 'hanoi')
        {
            $hanoi = new HanoiSoilModel();
            $hanoi->setContainer($this->getContainer());
            $hanoi->load();
        }
    }
}
