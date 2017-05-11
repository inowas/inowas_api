<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CopyModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function fromBaseModel(UserId $userId, ModflowId $baseModel, ModflowId $newModel): CopyModflowModel
    {
        return new self([
            'user_id' => $userId->toString(),
            'base_model_id' => $baseModel->toString(),
            'new_model_id' => $newModel->toString()
        ]);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function baseModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['base_model_id']);
    }

    public function newModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['new_model_id']);
    }
}
