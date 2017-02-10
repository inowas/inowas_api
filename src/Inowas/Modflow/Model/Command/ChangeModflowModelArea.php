<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelActiveCells;
use Inowas\Modflow\Model\ModflowModelArea;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\Polygon;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelArea extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, ModflowModelArea $area): ChangeModflowModelArea
    {
        $payload = [
            'modflow_model_id' => $modelId->toString(),
            'area' => serialize($area)
        ];

        return new self($payload);
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function area(): ModflowModelArea
    {
        return unserialize($this->payload['area']);
    }
}
