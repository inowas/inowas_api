<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\Layer;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddLayer extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(UserId $userId, ModflowId $modelId, Layer $layer): AddLayer
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'model_id' => $modelId->toString(),
                'layer' => $layer->toArray()
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

    public function layer(): Layer
    {
        return Layer::fromArray($this->payload['layer']);
    }
}
