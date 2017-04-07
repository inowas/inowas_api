<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Projection\ProjectionInterface;
use Prooph\EventStore\Stream\StreamName;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

ini_set('memory_limit', '2048M');

class ModflowProjectionCommand extends ContainerAwareCommand
{

    /** @var  \Inowas\Common\Id\UserId */
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
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.model_boundaries');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.model_boundary_values');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.model_scenarios');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.calculation_results');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.calculation_list');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.model_details');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.calculation_budgets');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.soilmodel_list');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.layer_details');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.layer_values');
        $projections[] = $this->getContainer()->get('inowas.modflow_projection.calculation_config');

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
