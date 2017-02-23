<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Modflow\Model\UserId;
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
            ->addArgument('model', InputArgument::REQUIRED, 'The model which to load')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('model') == 'hanoi')
        {
            $hanoi = new Hanoi();
            $hanoi->setContainer($this->getContainer());
            $hanoi->load();
        }
    }
}
