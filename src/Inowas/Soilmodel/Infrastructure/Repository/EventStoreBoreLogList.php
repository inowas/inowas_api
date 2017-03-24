<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Infrastructure\Repository;

use Inowas\Soilmodel\Model\BoreLogAggregate;
use Inowas\Soilmodel\Model\BoreLogId;
use Inowas\Soilmodel\Model\BoreLogList;

use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreBoreLogList extends AggregateRepository implements BoreLogList
{

    public function add(BoreLogAggregate $boreLogAggregate)
    {
        $this->addAggregateRoot($boreLogAggregate);
    }

    public function get(BoreLogId $boreLogId)
    {
        return $this->getAggregateRoot($boreLogId->toString());
    }
}
