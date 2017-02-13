<?php

namespace Inowas\Modflow\Model;

class WellBoundary extends ModflowBoundary
{
    public static function create(BoundaryId $boundaryId)
    {
        $static = new self();
        $static->boundaryId = $boundaryId;
        return $static;
    }
}
