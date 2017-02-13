<?php

namespace Inowas\Modflow\Model;

class ModflowBoundaryFactory
{
    public static function createFromIdAndType(BoundaryId $boundaryId, BoundaryType $type){
        if ($type->type() == BoundaryType::AREA) {
            return AreaBoundary::create($boundaryId);
        }

        if ($type->type() == BoundaryType::WELL) {
            return WellBoundary::create($boundaryId);
        }

        return null;
    }
}
