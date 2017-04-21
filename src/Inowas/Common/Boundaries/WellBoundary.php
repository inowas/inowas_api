<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class WellBoundary extends AbstractBoundary
{

    const TYPE = 'well';

    /** @var  LayerNumber */
    protected $layerNumber;

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
        LayerNumber $layerNumber
    ): WellBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        $self->layerNumber = $layerNumber;
        $self->wellType = $wellType;
        $self->observationPoint = ObservationPoint::fromIdNameAndGeometry(
            ObservationPointId::fromString($boundaryId->toString()),
            ObservationPointName::fromString($name->toString()),
            $geometry
        );
        return $self;
    }

    public function addPumpingRate(WellDateTimeValue $pumpingRate): WellBoundary
    {
        // In case of well, the observationPointId is the boundaryId
        $observationPointId = ObservationPointId::fromString($this->boundaryId->toString());
        if (! $this->hasOp($observationPointId)) {
            $this->addOp($this->createObservationPoint());
        }

        $this->addDateTimeValue($pumpingRate, $observationPointId);

        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->activeCells);
        $self->layerNumber = $this->layerNumber;
        $self->wellType = $this->wellType;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    private function createObservationPoint(): ObservationPoint
    {
        return ObservationPoint::fromIdNameAndGeometry(
            ObservationPointId::fromString($this->boundaryId->toString()),
            ObservationPointName::fromString($this->name->toString())
        );
    }

    public function setActiveCells(ActiveCells $activeCells): WellBoundary
    {
        $self = new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
        $self->layerNumber = $this->layerNumber;
        $self->wellType = $this->wellType;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function updateGeometry(Geometry $geometry): WellBoundary
    {
        $self = new self($this->boundaryId, $this->name, $geometry, $this->activeCells);
        $self->layerNumber = $this->layerNumber;
        $self->wellType = $this->wellType;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function layerNumber(): LayerNumber
    {
        return $this->layerNumber;
    }

    public function wellType(): WellType
    {
        return $this->wellType;
    }

    public function metadata(): array
    {
        return [
            'well_type' => $this->wellType->type(),
            'layer' => $this->layerNumber->toInteger()
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
