<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Projection\ProjectionInterface;
use Prooph\EventStore\Stream\StreamName;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowProjectionCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:projections:reset')
            ->setDescription('Rebuilds all projections');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projections = [];
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.boundaries');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.model_scenarios');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.calculation_results');


        /** @var ProjectionInterface $projection */
        foreach ($projections as $projection) {
            $projection->reset();
        }

        $eventBus = $this->getContainer()->get('prooph_service_bus.modflow_event_bus');
        $eventIterator = $this->getContainer()
            ->get('prooph_event_store.modflow_model_store')
            ->replay([new StreamName('event_stream')]);
        $eventIterator->rewind();

        while ($eventIterator->valid()) {
            $value = $eventIterator->current();
            $eventBus->dispatch($value);
            $eventIterator->next();
        }
    }
}
