<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\LayerId;
use Inowas\Common\Soilmodel\LayerProperty;

class LoadLayerDataFromRasterfile extends AbstractJsonSchemaCommand
{

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param LayerId $layerId
     * @param LayerProperty $property
     * @param \SplFileInfo $file
     * @return LoadLayerDataFromRasterfile
     */
    public static function fromParams(UserId $userId, ModflowId $modelId, LayerId $layerId, LayerProperty $property, \SplFileInfo $file): LoadLayerDataFromRasterfile
    {
        $self = new static(
            [
                'id' => $modelId->toString(),
                'layer_id' => $layerId->toString(),
                'property' => $property->toString(),
                'file' => $file->getRealPath()
            ]
        );

        /** @var LoadLayerDataFromRasterfile $self */
        $self = $self->withAddedMetadata('user_id', $userId->toString());
        return $self;
    }

    public function schema(): string
    {
        return 'file://spec/schema/modflow/command/loadLayerDataFromRasterfilePayload.json';
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->metadata['user_id']);
    }

    public function layerId(): LayerId
    {
        return LayerId::fromString($this->payload['layer_id']);
    }

    public function property(): LayerProperty
    {
        return LayerProperty::fromString($this->payload['property']);
    }

    public function file(): \SplFileInfo
    {
        return new \SplFileInfo($this->payload['file']);
    }
}
