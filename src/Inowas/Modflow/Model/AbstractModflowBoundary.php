<?php

namespace Inowas\Modflow\Model;

abstract class AbstractModflowBoundary implements ModflowBoundary
{
    /** @var  BoundaryId */
    protected $boundaryId;

    public function boundaryId(): BoundaryId
    {
        return $this->boundaryId;
    }
}
