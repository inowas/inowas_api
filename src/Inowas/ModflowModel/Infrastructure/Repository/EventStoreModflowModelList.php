<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Repository;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreModflowModelList extends AggregateRepository implements ModflowModelList
{
    /**
     * @param ModflowModelAggregate $model
     * @return void
     */
    public function add(ModflowModelAggregate $model)
    {
        $this->addAggregateRoot($model);
    }

    /**
     * @param \Inowas\Common\Id\ModflowId $modelId
     * @return mixed
     */
    public function get(ModflowId $modelId)
    {
        return $this->getAggregateRoot($modelId->toString());
    }
}
