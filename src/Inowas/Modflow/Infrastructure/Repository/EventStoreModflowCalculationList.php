<?php

declare(strict_types=1);

namespace Inowas\Modflow\Infrastructure\Repository;

use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreModflowCalculationList extends AggregateRepository implements ModflowModelCalculationList
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
