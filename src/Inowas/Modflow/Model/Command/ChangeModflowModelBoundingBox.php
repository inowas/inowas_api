<?php

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelBoundingBox;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelBoundingBox extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, ModflowModelBoundingBox $boundingBox): ChangeModflowModelBoundingBox
    {
        return new self(
            [
                'modflow_model_id' => $modelId->toString(),
                'bounding_box' =>
                    [
                        'x_min' => $boundingBox->xMin(),
                        'x_max' => $boundingBox->xMax(),
                        'y_min' => $boundingBox->yMin(),
                        'y_max' => $boundingBox->yMax(),
                        'srid' => $boundingBox->srid()
                    ]
            ]
        );
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function gridSize(): ModflowModelBoundingBox
    {
        return ModflowModelBoundingBox::fromCoordinates(
            $this->payload['bounding_box']['x_min'],
            $this->payload['bounding_box']['x_max'],
            $this->payload['bounding_box']['y_min'],
            $this->payload['bounding_box']['y_max'],
            $this->payload['bounding_box']['srind']
        );
    }
}
