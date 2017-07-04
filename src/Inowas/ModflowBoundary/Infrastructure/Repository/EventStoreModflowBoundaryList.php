<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Infrastructure\Repository;

use Inowas\Common\Id\BoundaryId;
use Inowas\ModflowBoundary\Model\ModflowBoundaryAggregate;
use Inowas\ModflowBoundary\Model\ModflowBoundaryList;
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
