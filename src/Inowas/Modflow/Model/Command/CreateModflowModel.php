<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function withId(ModflowModelId $modelId): CreateModflowModel
    {
        return new self(
            [
                'modflow_model_id' => $modelId->toString()
            ]
        );
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }
}
