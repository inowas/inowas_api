<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\ObservationPointNotFoundInBoundaryException;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

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

    /** @var array  */
    protected $observationPoints = [];

    protected function __construct(BoundaryId $boundaryId, BoundaryName $name = null, Geometry $geometry = null, ?ActiveCells $activeCells = null)
    {
        $this->boundaryId = $boundaryId;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->activeCells = $activeCells;

        if (is_null($this->name)) {
            $this->name = BoundaryName::fromString('');
        }
    }

    abstract public function setActiveCells(ActiveCells $activeCells);

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

    public function observationPoints(): array
    {
        return $this->observationPoints;
    }

    protected function addOp(ObservationPoint $point)
    {
        $this->observationPoints[$point->id()->toString()] = $point;
        return $this;
    }

    protected function hasOp(ObservationPointId $observationPointId): bool
    {
        return (array_key_exists($observationPointId->toString(), $this->observationPoints));
    }

    protected function getOp(ObservationPointId $observationPointId): ?ObservationPoint
    {
        if (! $this->hasOp($observationPointId)){
            return null;
        }

        return $this->observationPoints[$observationPointId->toString()];
    }

    protected function addDateTimeValue(DateTimeValue $dateTimeValue, ObservationPointId $observationPointId)
    {

        if (!array_key_exists($observationPointId->toString(), $this->observationPoints)){
            throw ObservationPointNotFoundInBoundaryException::withIds($this->boundaryId, $observationPointId);
        }

        /** @var ObservationPoint $observationPoint */
        $observationPoint = $this->observationPoints[$observationPointId->toString()];
        $observationPoint = $observationPoint->addDateTimeValue($dateTimeValue);
        $this->observationPoints[$observationPointId->toString()] = $observationPoint;

        return $this;
    }
}
