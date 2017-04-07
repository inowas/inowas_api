<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\ObservationPointNotFoundInBoundaryException;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class ConstantHeadBoundary extends AbstractBoundary
{
    const TYPE = 'chd';

    public static function create(BoundaryId $boundaryId): ConstantHeadBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry
    ): ConstantHeadBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        return $self;
    }

    public function addObservationPoint(ObservationPoint $point): ConstantHeadBoundary
    {
        $this->addOp($point);
        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->activeCells);
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function addConstantHeadToObservationPoint(ObservationPointId $observationPointId, ConstantHeadDateTimeValue $chdTimeValue): ConstantHeadBoundary
    {
        if (! $this->hasOp($observationPointId)){
            throw ObservationPointNotFoundInBoundaryException::withIds($this->boundaryId, $observationPointId);
        }

        $this->addDateTimeValue($chdTimeValue, $observationPointId);
        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->activeCells);
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function setActiveCells(ActiveCells $activeCells): ConstantHeadBoundary
    {
        return new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function metadata(): array
    {
        return [];
    }

    public function dataToJson(): string
    {
        return json_encode([]);
    }

    public function dateTimeValues(ObservationPointId $observationPointId): array
    {
        /** @var ObservationPoint $observationPoint */
        $observationPoint = $this->observationPoints[$observationPointId->toString()];
        return $observationPoint->dateTimeValues();
    }
}
