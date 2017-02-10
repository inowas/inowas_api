<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Inowas\Modflow\ModflowModel;

interface ModflowModelList
{
    public function add(ModflowModel $model);

    public function get(ModflowModelId $modelId);
}
