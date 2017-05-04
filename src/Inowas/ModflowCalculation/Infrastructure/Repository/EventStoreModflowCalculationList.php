<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Repository;

use Inowas\ModflowCalculation\Model\ModflowCalculationAggregate;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowCalculation\Model\ModflowCalculationList;
use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreModflowCalculationList extends AggregateRepository implements ModflowCalculationList
{

    public function add(ModflowCalculationAggregate $calculation)
    {
        $this->addAggregateRoot($calculation);
    }

    public function get(ModflowId $calculationId)
    {
        return $this->getAggregateRoot($calculationId->toString());
    }
}
