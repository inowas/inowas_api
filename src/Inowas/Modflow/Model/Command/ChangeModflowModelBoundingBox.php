<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelBoundingBox;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelBoundingBox extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(UserId $userId, ModflowModelId $modelId, ModflowModelBoundingBox $boundingBox): ChangeModflowModelBoundingBox
    {
        return new self(
            [
                'user_id' => $userId->toString(),
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

    public function boundingBox(): ModflowModelBoundingBox
    {
        return ModflowModelBoundingBox::fromCoordinates(
            $this->payload['bounding_box']['x_min'],
            $this->payload['bounding_box']['x_max'],
            $this->payload['bounding_box']['y_min'],
            $this->payload['bounding_box']['y_max'],
            $this->payload['bounding_box']['srid']
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }
}
