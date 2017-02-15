<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\SoilModelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelSoilmodelId extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowId $modelId, SoilModelId $soilModelId): ChangeModflowModelSoilmodelId
    {
        return new self(
            [
                'modflow_model_id' => $modelId->toString(),
                'soilmodel_id' => $soilModelId->toString()
            ]
        );
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflow_model_id']);
    }

    public function soilModelId(): ?SoilModelId
    {
        return SoilModelId::fromString($this->payload['soilmodel_id']);
    }
}
