<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelBoundingBoxWasChanged extends AggregateChanged
{

    /** @var  \Inowas\Common\Id\ModflowId */
    private $modflowModelId;

    /** @var \Inowas\Common\Grid\BoundingBox */
    private $boundingBox;

    /** @var  UserId */
    private $userId;

    public static function withBoundingBox(UserId $userId, ModflowId $modflowModelId, BoundingBox $boundingBox): ModflowModelBoundingBoxWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'bounding_box' => [
                    'x_min' => $boundingBox->xMin(),
                    'x_max' => $boundingBox->xMax(),
                    'y_min' => $boundingBox->yMin(),
                    'y_max' => $boundingBox->yMax(),
                    'srid' => $boundingBox->srid(),
                ]
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundingBox = $boundingBox;
        $event->userId = $userId;

        return $event;
    }

    public function modflowModelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function boundingBox(): BoundingBox
    {
        if ($this->boundingBox === null){
            $this->boundingBox = BoundingBox::fromCoordinates(
                $this->payload['bounding_box']['x_min'],
                $this->payload['bounding_box']['x_max'],
                $this->payload['bounding_box']['y_min'],
                $this->payload['bounding_box']['y_max'],
                $this->payload['bounding_box']['srid']
            );
        }

        return $this->boundingBox;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
