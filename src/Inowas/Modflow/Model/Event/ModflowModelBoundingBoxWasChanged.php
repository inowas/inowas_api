<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelBoundingBox;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelBoundingBoxWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowModelBoundingBox */
    private $boundingBox;

    public static function withBoundingBox(ModflowModelId $modflowModelId, ModflowModelBoundingBox $boundingBox): ModflowModelBoundingBoxWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'bounding_box' => [
                    'x_min' => $boundingBox->xMin(),
                    'x_max' => $boundingBox->xMax(),
                    'y_min' => $boundingBox->yMin(),
                    'y_max' => $boundingBox->yMax(),
                ]
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundingBox = $boundingBox;

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
            $this->boundingBox = ModflowModelBoundingBox::fromEPSG4326Coordinates(
                $this->payload['bounding_box']['x_min'],
                $this->payload['bounding_box']['x_max'],
                $this->payload['bounding_box']['y_min'],
                $this->payload['bounding_box']['y_max']
            );
        }

        return $this->boundingBox;
    }
}
