<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\ModflowModel;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class DeleteModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byIdAndUser(ModflowId $modelId, UserId $userId): DeleteModflowModel
    {
        return new self([
            'user_id' => $userId->toString(),
            'modflowmodel_id' => $modelId->toString()
        ]);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflowmodel_id']);
    }
}
