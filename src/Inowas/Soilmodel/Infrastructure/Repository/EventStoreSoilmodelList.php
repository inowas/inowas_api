<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Infrastructure\Repository;

use Inowas\Soilmodel\Model\SoilmodelAggregate;
use Inowas\Soilmodel\Model\SoilmodelId;
use Inowas\Soilmodel\Model\SoilmodelList;
use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreSoilmodelList extends AggregateRepository implements SoilmodelList
{

    public function add(SoilmodelAggregate $soilmodelAggregate)
    {
        $this->addAggregateRoot($soilmodelAggregate);
    }

    public function get(SoilmodelId $soilmodelId)
    {
        return $this->getAggregateRoot($soilmodelId->toString());
    }
}