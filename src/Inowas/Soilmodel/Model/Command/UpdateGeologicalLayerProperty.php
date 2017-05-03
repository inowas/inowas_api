<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\AbstractSoilproperty;
use Inowas\Common\Soilmodel\GeologicalLayerId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateGeologicalLayerProperty extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forSoilmodel(UserId $userId, SoilmodelId $id, GeologicalLayerId $layerId, AbstractSoilproperty $property): UpdateGeologicalLayerProperty
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'soilmodel_id' => $id->toString(),
                'layer_id' => $layerId->toString(),
                'property' => serialize($property)
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function soilModelId(): SoilmodelId
    {
        return SoilmodelId::fromString($this->payload['soilmodel_id']);
    }

    public function layerId(): GeologicalLayerId
    {
        return GeologicalLayerId::fromString($this->payload['layer_id']);
    }

    public function property(): AbstractSoilproperty
    {
        return unserialize($this->payload['property']);
    }
}
