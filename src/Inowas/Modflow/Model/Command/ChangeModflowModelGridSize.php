<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelGridSize;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelGridSize extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, ModflowModelGridSize $gridSize): ChangeModflowModelGridSize
    {
        return new self(
            [
                'modflow_model_id' => $modelId->toString(),
                'grid_size' =>
                    [
                        'nX' => $gridSize->nX(),
                        'nY' => $gridSize->nY()
                    ]
            ]
        );
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function gridSize(): ModflowModelGridSize
    {
        return ModflowModelGridSize::fromXY(
            $this->payload['grid_size']['nX'],
            $this->payload['grid_size']['nY']
        );
    }
}
