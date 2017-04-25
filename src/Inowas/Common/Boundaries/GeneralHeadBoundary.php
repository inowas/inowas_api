<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\ObservationPointNotFoundInBoundaryException;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class GeneralHeadBoundary extends AbstractBoundary
{
    const TYPE = 'ghb';

    public static function create(BoundaryId $boundaryId): GeneralHeadBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        AffectedLayers $affectedLayers
    ): GeneralHeadBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        $self->affectedLayers = $affectedLayers;
        return $self;
    }

    public function addObservationPoint(ObservationPoint $point): GeneralHeadBoundary
    {
        $this->addOp($point);
        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->activeCells);
        $self->affectedLayers = $this->affectedLayers;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function addGeneralHeadValueToObservationPoint(ObservationPointId $observationPointId, GeneralHeadDateTimeValue $ghbDateTimeValue): GeneralHeadBoundary
    {
        if (! $this->hasOp($observationPointId)){
            throw ObservationPointNotFoundInBoundaryException::withIds($this->boundaryId, $observationPointId);
        }

        $this->addDateTimeValue($ghbDateTimeValue, $observationPointId);
        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->activeCells);
        $self->affectedLayers = $this->affectedLayers;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function setActiveCells(ActiveCells $activeCells): GeneralHeadBoundary
    {
        $self = new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
        $self->affectedLayers = $this->affectedLayers;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function updateGeometry(Geometry $geometry): GeneralHeadBoundary
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

    public function findValueByDateTime(\DateTimeImmutable $dateTime): ?GeneralHeadDateTimeValue
    {
        /** @var ObservationPoint $op */
        #$op = $this->getOp(ObservationPointId::fromString($this->boundaryId->toString()));
        $op = array_values($this->observationPoints)[0];
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof GeneralHeadDateTimeValue){
            return $value;
        }

        return null;
    }

    public function findValueByDateTimeAndObservationPointId(\DateTimeImmutable $dateTime, ObservationPointId $observationPointId): ?GeneralHeadDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getOp($observationPointId);
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof GeneralHeadDateTimeValue){
            return $value;
        }

        return null;
    }
}
