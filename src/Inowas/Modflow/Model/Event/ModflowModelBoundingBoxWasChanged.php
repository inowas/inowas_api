<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelBoundingBox;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelBoundingBoxWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowModelBoundingBox */
    private $boundingBox;

    /** @var  UserId */
    private $userId;

    public static function withBoundingBox(UserId $userId, ModflowModelId $modflowModelId, ModflowModelBoundingBox $boundingBox): ModflowModelBoundingBoxWasChanged
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

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function boundingBox(): ModflowModelBoundingBox
    {
        if ($this->boundingBox === null){
            $this->boundingBox = ModflowModelBoundingBox::fromCoordinates(
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
