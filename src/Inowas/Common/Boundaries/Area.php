<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;

class Area
{
    const TYPE = 'area';

    /** @var  BoundaryId */
    protected $boundaryId;

    /** @var  BoundaryName */
    protected $name;

    /** @var  Polygon */
    protected $polygon;

    /** @var  ActiveCells */
    protected $activeCells;

    public static function create(BoundaryId $boundaryId, BoundaryName $name, Polygon $polygon): Area
    {
        return new self($boundaryId, $name, $polygon);
    }

    protected function __construct(BoundaryId $boundaryId, BoundaryName $name, Polygon $polygon, ?ActiveCells $activeCells = null)
    {
        $this->boundaryId = $boundaryId;
        $this->name = $name;
        $this->polygon = $polygon;
        $this->activeCells = $activeCells;
    }

    public function setName(BoundaryName $name): Area
    {
        return new self($this->boundaryId, $name, $this->polygon, $this->activeCells);
    }

    public function setGeometry(Polygon $polygon): Area
    {
        return new self($this->boundaryId, $this->name, $polygon, $this->activeCells);
    }

    public function setActiveCells(ActiveCells $activeCells): Area
    {
        return new self($this->boundaryId, $this->name, $this->polygon, $activeCells);
    }

    public function updateGeometry(Polygon $polygon): Area
    {
        return new self($this->boundaryId, $this->name, $polygon, $this->activeCells);
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function boundaryId(): BoundaryId
    {
        return $this->boundaryId;
    }

    public function geometry(): Polygon
    {
        return $this->polygon;
    }

    public function name(): BoundaryName
    {
        return $this->name;
    }

    public function activeCells(): ?ActiveCells
    {
        return $this->activeCells;
    }

    public function metadata(): array
    {
        return [];
    }
}
