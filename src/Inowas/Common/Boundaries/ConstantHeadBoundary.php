<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\ObservationPointNotFoundInBoundaryException;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
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
        Geometry $geometry,
        AffectedLayers $affectedLayers
    ): ConstantHeadBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        $self->affectedLayers = $affectedLayers;
        return $self;
    }

    public function addObservationPoint(ObservationPoint $point): ConstantHeadBoundary
    {
        $this->addOp($point);
        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->activeCells);
        $self->affectedLayers = $this->affectedLayers;
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
        $self->affectedLayers = $this->affectedLayers;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function setActiveCells(ActiveCells $activeCells): ConstantHeadBoundary
    {
        $self = new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
        $self->affectedLayers = $this->affectedLayers;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function updateGeometry(Geometry $geometry): ConstantHeadBoundary
    {
        $self = new self($this->boundaryId, $this->name, $geometry, $this->activeCells);
        $self->affectedLayers = $this->affectedLayers;
        $self->observationPoints = $this->observationPoints;
        return $self;
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

    public function findValueByDateTime(\DateTimeImmutable $dateTime): ?ConstantHeadDateTimeValue
    {
        /** @var ObservationPoint $op */
        #$op = $this->getOp(ObservationPointId::fromString($this->boundaryId->toString()));
        $op = array_values($this->observationPoints)[0];
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof ConstantHeadDateTimeValue){
            return $value;
        }

        return null;
    }
}
