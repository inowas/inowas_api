<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Repository;

use Inowas\Common\Id\BoundaryId;
use Inowas\ModflowModel\Model\ModflowBoundaryAggregate;
use Inowas\ModflowModel\Model\ModflowBoundaryList;
use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreModflowBoundaryList extends AggregateRepository implements ModflowBoundaryList
{

    public function add(ModflowBoundaryAggregate $calculation): void
    {
        $this->addAggregateRoot($calculation);
    }

    public function get(BoundaryId $boundaryId)
    {
        return $this->getAggregateRoot($boundaryId->toString());
    }
}
