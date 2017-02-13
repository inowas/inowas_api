<?php

namespace Inowas\Modflow\Model;

class WellBoundary extends AbstractModflowBoundary
{
    public static function create(BoundaryId $boundaryId)
    {
        $static = new self();
        $static->boundaryId = $boundaryId;
        return $static;
    }
}
