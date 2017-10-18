<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ToolReadModelProjectorCommand extends ContainerAwareCommand
{

    protected function configure(): void
    {
        $this
            ->setName('inowas:projector:run')
            ->setDescription('Tool Projector Service')
            ->addArgument('name',
            InputOption::VALUE_REQUIRED
            )
            ->addOption(
                'reset',
                'r',
                InputOption::VALUE_NONE,
                'Reset Projector?'
            )
            ->addOption(
                'delete',
                'd',
                InputOption::VALUE_NONE,
                'Delete Projector?'
            )
            ->addOption(
                'loop',
                'l',
                InputOption::VALUE_NONE,
                'Keep running?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reset = $input->getOption('reset');
        $delete = $input->getOption('delete');
        $loop = $input->getOption('loop');
        $name = $input->getArgument('name');


        $tpr = $this->getContainer()->get('inowas.projections.' . $name);

        if($reset) {
            $tpr->reset();
            return;
        }

        if($delete) {
            $tpr->delete();
            return;
        }

        $tpr($loop);
    }
}
