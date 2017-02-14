<?php

namespace Inowas\Modflow\Model;

abstract class AbstractModflowBoundary implements ModflowBoundary
{
    /** @var  BoundaryId */
    protected $boundaryId;

    /** @var  BoundaryName */
    protected $name;

    /** @var  BoundaryGeometry */
    protected $geometry;

    protected function __construct(BoundaryId $boundaryId, BoundaryName $name = null, BoundaryGeometry $geometry = null)
    {
        $this->boundaryId = $boundaryId;
        $this->name = $name;
        $this->geometry = $geometry;
    }

    public function boundaryId(): BoundaryId
    {
        return $this->boundaryId;
    }

    public function name(): BoundaryName
    {
        return $this->name;
    }

    public function geometry(): BoundaryGeometry
    {
        return $this->geometry;
    }
}
