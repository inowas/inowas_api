<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

interface ModflowModelList
{
    public function add(ModflowModelAggregate $model);

    public function get(ModflowId $modelId);
}
