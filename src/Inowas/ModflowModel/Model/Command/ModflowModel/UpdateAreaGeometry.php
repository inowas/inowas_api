<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\ModflowModel;

use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateAreaGeometry extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function of(
        UserId $userId,
        ModflowId $modelId,
        Polygon $polygon
    ): UpdateAreaGeometry
    {
        $payload = [
            'user_id' => $userId->toString(),
            'model_id' => $modelId->toString(),
            'geometry' => serialize($polygon)
        ];

        return new self($payload);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['model_id']);
    }

    public function geometry(): Polygon
    {
        return unserialize($this->payload['geometry']);
    }
}
