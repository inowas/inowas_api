<?php

namespace Inowas\Modflow\Model;

abstract class ModflowBoundary
{
    /** @var  BoundaryId */
    protected $boundaryId;

    abstract public static function create(BoundaryId $boundaryId);

    public function boundaryId(){
        return $this->boundaryId;
    }
}
