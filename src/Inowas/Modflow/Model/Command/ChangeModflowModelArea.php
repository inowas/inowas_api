<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelActiveCells;
use Inowas\Modflow\Model\ModflowModelArea;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelArea extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, ModflowModelArea $area): ChangeModflowModelArea
    {
        return new self(
            [
                'modflow_model_id' => $modelId->toString(),
                'area' =>
                    [
                        'active_cells' => $area->activeCells(),
                        'geometry' => $area->geometry()
                    ]
            ]
        );
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function gridSize(): ModflowModelArea
    {
        $area = ModflowModelArea::fromPolygon(
            $this->payload['area']['geometry']
        );

        if ($this->payload['area']['active_cells'] instanceof ModflowModelActiveCells){
            $area->setActiveCells($this->payload['area']['active_cells']);
        }

        return $area;
    }
}
