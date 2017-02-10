<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelDescription extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, ModflowModelDescription $description): ChangeModflowModelDescription
    {
        return new self(
            [
                'modflow_model_id' => $modelId->toString(),
                'description' => $description->toString()
            ]
        );
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function name(): ModflowModelDescription
    {
        return ModflowModelDescription::fromString($this->payload['description']);
    }
}
