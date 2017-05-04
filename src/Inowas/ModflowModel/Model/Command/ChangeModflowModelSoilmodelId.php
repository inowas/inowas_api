<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelSoilmodelId extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowId $modelId, SoilmodelId $soilModelId): ChangeModflowModelSoilmodelId
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

    public function soilModelId(): ?SoilmodelId
    {
        return SoilmodelId::fromString($this->payload['soilmodel_id']);
    }
}
