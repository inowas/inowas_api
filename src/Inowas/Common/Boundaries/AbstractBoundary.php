<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\ObservationPointNotFoundInBoundaryException;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\LayerNumber;
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

    /** @var  AffectedLayers */
    protected $affectedLayers;

    /** @var array  */
    protected $observationPoints = [];

    protected function __construct(BoundaryId $boundaryId, BoundaryName $name = null, Geometry $geometry = null, ?ActiveCells $activeCells = null)
    {
        $this->boundaryId = $boundaryId;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->activeCells = $activeCells;
        $this->affectedLayers = AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0));

        if (null === $this->name) {
            $this->name = BoundaryName::fromString('');
        }
    }

    abstract public function setActiveCells(ActiveCells $activeCells);

    abstract public function updateGeometry(Geometry $geometry);

    public function activeCells(): ?ActiveCells
    {
        return $this->activeCells;
    }

    public function affectedLayers(): AffectedLayers
    {
        return $this->affectedLayers;
    }

    public function boundaryId(): BoundaryId
    {
        return $this->boundaryId;
    }

    public function geometry(): ?Geometry
    {
        return $this->geometry;
    }

    public function name(): ?BoundaryName
    {
        return $this->name;
    }

    public function updateName(BoundaryName $boundaryName): void
    {
        $this->name = BoundaryName::fromString($boundaryName->toString());
    }

    public function observationPoints(): array
    {
        return $this->observationPoints;
    }

    public function getObservationPoint(ObservationPointId $id): ?ObservationPoint
    {
        return $this->getOp($id);
    }

    public function updateObservationPoint(ObservationPoint $op): void
    {
        if ($this->hasOp($op->id())){
            $this->addOrUpdateOp($op);
        }
    }

    protected function addOrUpdateOp(ObservationPoint $point): void
    {
        $this->observationPoints[$point->id()->toString()] = $point;
    }

    protected function hasOp(ObservationPointId $observationPointId): bool
    {
        return array_key_exists($observationPointId->toString(), $this->observationPoints);
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
