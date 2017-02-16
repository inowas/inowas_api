<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowUser;
use Inowas\Modflow\Model\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateModflowModelCalculation extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithModelId(ModflowId $calculationId, UserId $userId, ModflowId $modelId): CreateModflowModelCalculation
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'calculation_id' => $calculationId->toString(),
                'modflow_model_id' => $modelId->toString()
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflow_model_id']);
    }

    public function calculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['calculation_id']);
    }
}
