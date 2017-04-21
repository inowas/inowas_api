<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class RechargeBoundary extends AbstractBoundary
{
    const TYPE = 'rch';

    /** @var  ObservationPoint */
    protected $observationPoint;

    public static function create(BoundaryId $boundaryId): RechargeBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry
    ): RechargeBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        return $self;
    }

    public function addRecharge(RechargeDateTimeValue $rechargeRate): RechargeBoundary
    {
        // In case of rechargeBoundary, the observationPointId is the boundaryId
        $observationPointId = ObservationPointId::fromString($this->boundaryId->toString());
        if (! $this->hasOp($observationPointId)) {
            $this->addOp($this->createObservationPoint());
        }

        $this->addDateTimeValue($rechargeRate, $observationPointId);

        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->activeCells);
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    public function setActiveCells(ActiveCells $activeCells): RechargeBoundary
    {
        return new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
    }

    public function updateGeometry(Geometry $geometry): RechargeBoundary
    {
        return new self($this->boundaryId, $this->name, $geometry, $this->activeCells);
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
        return json_encode($this->observationPoints);
    }

    public function dateTimeValues(): array
    {
        /** @var ObservationPoint $observationPoint */
        $observationPoint = $this->observationPoints[$this->boundaryId->toString()];
        return $observationPoint->dateTimeValues();
    }

    private function createObservationPoint(): ObservationPoint
    {
        return ObservationPoint::fromIdNameAndGeometry(
            ObservationPointId::fromString($this->boundaryId->toString()),
            ObservationPointName::fromString($this->name->toString()),
            $this->geometry
        );
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): RechargeDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getOp(ObservationPointId::fromString($this->boundaryId->toString()));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof RechargeDateTimeValue){
            return $value;
        }

        return RechargeDateTimeValue::fromParams($dateTime, 0);
    }
}
