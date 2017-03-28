<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;

abstract class AbstractBoundary implements ModflowBoundary
{
    /** @var  BoundaryId */
    protected $boundaryId;

    /** @var  BoundaryName */
    protected $name;

    /** @var  Geometry */
    protected $geometry;

    /** @var  ActiveCells */
    protected $activeCells;

    protected function __construct(BoundaryId $boundaryId, BoundaryName $name = null, Geometry $geometry = null, ActiveCells $activeCells = null)
    {
        $this->boundaryId = $boundaryId;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->activeCells = $activeCells;
    }

    public function boundaryId(): BoundaryId
    {
        return $this->boundaryId;
    }

    public function name(): ?BoundaryName
    {
        return $this->name;
    }

    public function geometry(): ?Geometry
    {
        return $this->geometry;
    }

    public function activeCells(): ?ActiveCells
    {
        return $this->activeCells;
    }

    abstract public function setActiveCells(ActiveCells $activeCells);
}
