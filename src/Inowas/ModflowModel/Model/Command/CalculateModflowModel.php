<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CalculateModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModelWitUserId(UserId $userId, ModflowId $modelId): CalculateModflowModel
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'from_terminal' => false
            ]
        );
    }

    public static function forModflowModelFromTerminal(ModflowId $modelId): CalculateModflowModel
    {
        return new self(
            [
                'modflow_model_id' => $modelId->toString(),
                'from_terminal' => true
            ]
        );
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflow_model_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function fromTerminal(): bool
    {
        return $this->payload['from_terminal'];
    }
}
