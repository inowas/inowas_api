<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\BoundaryType;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddModflowModelBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, BoundaryId $boundaryId, BoundaryType $boundaryType): AddModflowModelBoundary
    {
        $payload = [
            'modflow_model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'boundary_type' => $boundaryType->type()
        ];

        return new self($payload);
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function boundaryId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['boundary_id']);
    }

    public function boundaryType(): BoundaryType
    {
        return BoundaryType::fromString($this->payload['boundary_type']);
    }
}
