<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model;

use Inowas\Common\Id\ModflowId;

interface ModflowModelList
{
    public function add(ModflowModelAggregate $model);

    public function get(ModflowId $modelId);
}