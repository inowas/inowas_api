<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\ObservationPointNotFoundInBoundaryException;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
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

    /** @var  AffectedLayers */
    protected $affectedLayers;

    /** @var  BoundaryMetadata */
    protected $metadata;

    /** @var  ActiveCells */
    protected $activeCells;

    /** @var array  */
    protected $observationPoints = [];

    abstract protected function self(): ModflowBoundary;

    protected function __construct(BoundaryId $boundaryId, BoundaryName $name, Geometry $geometry, AffectedLayers $affectedLayers, BoundaryMetadata $metadata)
    {
        $this->boundaryId = $boundaryId;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->affectedLayers = $affectedLayers;
        $this->metadata = $metadata;
    }

    public function updateName(BoundaryName $boundaryName): ModflowBoundary
    {
        $this->name = $boundaryName;
        return $this->self();
    }

    public function updateGeometry(Geometry $geometry): ModflowBoundary
    {
        $this->geometry = $geometry;
        return $this->self();
    }

    public function updateAffectedLayers(AffectedLayers $affectedLayers): ModflowBoundary
    {
        $this->affectedLayers = $affectedLayers;
        return $this->self();
    }

    public function updateMetadata(BoundaryMetadata $metadata): ModflowBoundary
    {
        $this->metadata = $metadata;
        return $this->self();
    }

    public function setActiveCells(ActiveCells $activeCells): ModflowBoundary
    {
        $this->activeCells = $activeCells;
        return $this->self();
    }

    public function addObservationPoint(ObservationPoint $point): ModflowBoundary
    {
        $this->addOrUpdateOp($point);
        return $this->self();
    }

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

    public function geometry(): Geometry
    {
        return $this->geometry;
    }

    public function name(): ?BoundaryName
    {
        return $this->name;
    }

    public function metadata(): BoundaryMetadata
    {
        if (null === $this->metadata){
            $this->metadata = BoundaryMetadata::create();
        }

        return $this->metadata;
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

    public function dateTimeValues(ObservationPointId $observationPointId): array
    {
        /** @var ObservationPoint $observationPoint */
        $observationPoint = $this->observationPoints[$observationPointId->toString()];
        return $observationPoint->dateTimeValues();
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
