<?php

namespace Inowas\Modflow\Model;

class AreaBoundary extends AbstractModflowBoundary
{
    public static function create(BoundaryId $boundaryId)
    {
        $static = new self();
        $static->boundaryId = $boundaryId;
        return $static;
    }
}
