<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelAreaId extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, BoundaryId $boundaryId): ChangeModflowModelAreaId
    {
        $payload = [
            'modflow_model_id' => $modelId->toString(),
            'area_id' => $boundaryId->toString()
        ];

        return new self($payload);
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function areaId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['area_id']);
    }
}
