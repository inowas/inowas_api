<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

interface ModflowModelList
{
    public function add(ModflowModel $model);

    public function get(ModflowModelId $modelId);
}
