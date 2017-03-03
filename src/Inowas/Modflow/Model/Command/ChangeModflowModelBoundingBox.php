<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelBoundingBox extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(UserId $userId, ModflowId $modelId, BoundingBox $boundingBox): ChangeModflowModelBoundingBox
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

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflow_model_id']);
    }

    public function boundingBox(): BoundingBox
    {
        return BoundingBox::fromCoordinates(
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
