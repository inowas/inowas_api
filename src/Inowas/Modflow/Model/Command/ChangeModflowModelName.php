<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ModflowModelName;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelName extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, ModflowModelName $modelName): ChangeModflowModelName
    {
        return new self(
            [
                'modflow_model_id' => $modelId->toString(),
                'name' => $modelName->toString()
            ]
        );
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function name(): ModflowModelName
    {
        return ModflowModelName::fromString($this->payload['name']);
    }
}
