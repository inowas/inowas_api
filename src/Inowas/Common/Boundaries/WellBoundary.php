<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class WellBoundary extends AbstractBoundary
{

    const TYPE = 'wel';

    /** @var  WellType */
    protected $wellType;

    public static function create(BoundaryId $boundaryId): WellBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        WellType $wellType,
        AffectedLayers $affectedLayers
    ): WellBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        $self->wellType = $wellType;
        $self->affectedLayers = $affectedLayers;
        return $self;
    }

    public function addPumpingRate(WellDateTimeValue $pumpingRate): WellBoundary
    {
        // In case of well, the observationPointId is the boundaryId
        $observationPointId = ObservationPointId::fromString($this->boundaryId->toString());
        if (! $this->hasOp($observationPointId)) {
            $this->addOrUpdateOp(
                ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($this->boundaryId->toString()),
                    ObservationPointName::fromString($this->name->toString()),
                    $this->geometry
                )
            );
        }

        $this->addDateTimeValue($pumpingRate, $observationPointId);

        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->activeCells);
        $self->affectedLayers = $this->affectedLayers;
        $self->wellType = $this->wellType;
        $self->observationPoints = $this->observationPoints;
        $self->affectedLayers = $this->affectedLayers;
        return $self;
    }

    public function setActiveCells(ActiveCells $activeCells): WellBoundary
    {
        $self = new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
        $self->wellType = $this->wellType;
        $self->observationPoints = $this->observationPoints;
        $self->affectedLayers = $this->affectedLayers;
        return $self;
    }

    public function updateGeometry(Geometry $geometry): WellBoundary
    {
        $self = new self($this->boundaryId, $this->name, $geometry, $this->activeCells);
        $self->affectedLayers = $this->affectedLayers;
        $self->wellType = $this->wellType;

        /** @var ObservationPoint $observationPoint */
        $observationPoint = array_values($this->observationPoints)[0];
        $changedObservationPoint = ObservationPoint::fromIdNameAndGeometry($observationPoint->id(), $observationPoint->name(), $geometry);
        $this->observationPoints[$changedObservationPoint->id()->toString()] = $changedObservationPoint;
        $self->observationPoints = $this->observationPoints;
        $self->affectedLayers = $this->affectedLayers;
        return $self;
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function wellType(): WellType
    {
        return $this->wellType;
    }

    public function metadata(): array
    {
        return [
            'well_type' => $this->wellType->type(),
            'layer' => $this->affectedLayers->toArray()
        ];
    }

    public function dataToJson(): string
    {
        return json_encode($this->observationPoints);
    }

    public function dateTimeValues(): array
    {
        /** @var ObservationPoint $observationPoint */
        $observationPoint = $this->observationPoints[$this->boundaryId->toString()];
        return $observationPoint->dateTimeValues();
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): WellDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getOp(ObservationPointId::fromString($this->boundaryId->toString()));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof WellDateTimeValue){
            return $value;
        }

        return WellDateTimeValue::fromParams($dateTime, 0);
    }
}
