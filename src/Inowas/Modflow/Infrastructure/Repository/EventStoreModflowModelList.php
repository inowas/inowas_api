<?php

declare(strict_types=1);

namespace Inowas\Modflow\Infrastructure\Repository;

use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModel;
use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreModflowModelList extends AggregateRepository implements ModflowModelList
{
    /**
     * @param ModflowModel $model
     * @return void
     */
    public function add(ModflowModel $model)
    {
        $this->addAggregateRoot($model);
    }

    /**
     * @param ModflowModelId $modelId
     * @return mixed
     */
    public function get(ModflowModelId $modelId)
    {
        return $this->getAggregateRoot($modelId->toString());
    }
}
