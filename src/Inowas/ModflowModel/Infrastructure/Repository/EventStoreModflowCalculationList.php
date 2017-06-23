<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Repository;

use Inowas\Common\Id\CalculationId;
use Inowas\ModflowCalculation\Model\ModflowCalculationAggregate;
use Inowas\ModflowModel\Model\ModflowCalculationList;
use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreModflowCalculationList extends AggregateRepository implements ModflowCalculationList
{

    public function add(ModflowCalculationAggregate $calculation): void
    {
        $this->addAggregateRoot($calculation);
    }

    public function get(CalculationId $calculationId)
    {
        return $this->getAggregateRoot($calculationId->toString());
    }
}
