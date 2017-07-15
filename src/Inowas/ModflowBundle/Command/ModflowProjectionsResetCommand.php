<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\UserId;
use Inowas\Common\Projection\ProjectionInterface;
use Prooph\EventStore\Stream\StreamName;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

ini_set('memory_limit', '2048M');

class ModflowProjectionsResetCommand extends ContainerAwareCommand
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
        $projections[] = $this->getContainer()->get('inowas.modflowmodel.active_cells_projector');
        $projections[] = $this->getContainer()->get('inowas.modflowmodel.boundary_list_projector');
        $projections[] = $this->getContainer()->get('inowas.modflowmodel.calculation_results_projector');
        $projections[] = $this->getContainer()->get('inowas.modflowmodel.model_projector');
        $projections[] = $this->getContainer()->get('inowas.tool.tools_projector');
        $projections[] = $this->getContainer()->get('inowas.scenarioanalysis.scenarioanalysis_list_projector');
        $projections[] = $this->getContainer()->get('inowas.scenarioanalysis.scenario_list_projector');
        $projections[] = $this->getContainer()->get('inowas.soilmodel.soilmodel_list_projector');
        $projections[] = $this->getContainer()->get('inowas.soilmodel.layer_values_projector');

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
