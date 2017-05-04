<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithModelId(UserId $userId, ModflowId $modelId): CreateModflowModel
    {
        return new self(
            [
                'user_id' => $userId->toString(),
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
}
