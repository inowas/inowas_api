<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\LayerId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class RemoveLayer extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(UserId $userId, ModflowId $modelId, LayerId $layerId): RemoveLayer
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'model_id' => $modelId->toString(),
                'layer_id' => $layerId->toString()
            ]
        );
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['model_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function layerId(): LayerId
    {
        return LayerId::fromString($this->payload['layer_id']);
    }
}
