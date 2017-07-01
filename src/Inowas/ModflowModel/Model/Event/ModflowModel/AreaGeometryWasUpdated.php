<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event\ModflowModel;

use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class AreaGeometryWasUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowModelId;

    /** @var  Polygon */
    private $geometry;

    /** @var  UserId */
    private $userId;

    public static function of(ModflowId $modflowModelId, UserId $userId, Polygon $polygon): AreaGeometryWasUpdated
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'geometry' => serialize($polygon)
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->geometry = $polygon;

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function geometry(): Polygon
    {
        if ($this->geometry === null){
            $this->geometry = unserialize($this->payload['geometry']);
        }

        return $this->geometry;
    }
}
