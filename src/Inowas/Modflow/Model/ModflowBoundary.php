<?php

namespace Inowas\Modflow\Model;

interface ModflowBoundary
{
    public static function create(BoundaryId $boundaryId);

    public function boundaryId(): BoundaryId;
}
