<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;

abstract class AbstractBoundary implements ModflowBoundary
{
    /** @var  BoundaryId */
    protected $boundaryId;

    /** @var  BoundaryName */
    protected $name;

    /** @var  Geometry */
    protected $geometry;

    protected function __construct(BoundaryId $boundaryId, BoundaryName $name = null, Geometry $geometry = null)
    {
        $this->boundaryId = $boundaryId;
        $this->name = $name;
        $this->geometry = $geometry;
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
}
