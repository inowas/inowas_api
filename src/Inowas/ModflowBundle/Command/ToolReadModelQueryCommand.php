<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\UserId;
use Inowas\Common\Projection\ProjectionInterface;
use Inowas\Tool\Infrastructure\Projection\Table;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\StreamName;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ToolReadModelQueryCommand extends ContainerAwareCommand
{

    protected function configure(): void
    {
        $this
            ->setName('inowas:tools:query')
            ->setDescription('Tool Projector Service')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectionManager = $this->getContainer()->get('inowas.tool.projection_manager');
        $query = $projectionManager->createQuery();

        $query
            ->init(function (): array {
                return ['count' => 0];
            })
            ->fromAll()
            ->whenAny(function (
                    array $state, Message $event
                ) use ($output): array {
                    $output->writeln($event->messageName() . ' ' . $event->createdAt()->format(DATE_ATOM));
                    $state['count']++;
                    return $state;
                }
            )
            ->run();

        $output->writeln($query->getState()['count']);
    }
}
