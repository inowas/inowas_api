<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class WellBoundary extends AbstractBoundary
{

    const TYPE = 'wel';

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $boundaryId
     * @param BoundaryName $name
     * @param Geometry $geometry
     * @param AffectedLayers $affectedLayers
     * @param BoundaryMetadata $metadata
     * @return WellBoundary
     */
    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        AffectedLayers $affectedLayers,
        BoundaryMetadata $metadata
    ): WellBoundary
    {
        return new self($boundaryId, $name, $geometry, $affectedLayers, $metadata);
    }

    public function type(): BoundaryType
    {
        return BoundaryType::fromString($this::TYPE);
    }

    public function addPumpingRate(WellDateTimeValue $pumpingRate): ModflowBoundary
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
        return $this->self();
    }

    public function updateGeometry(Geometry $geometry): ModflowBoundary
    {
        /** @var ObservationPoint $observationPoint */
        $observationPoint = array_values($this->observationPoints)[0];
        $changedObservationPoint = ObservationPoint::fromIdNameAndGeometry($observationPoint->id(), $observationPoint->name(), $geometry);
        $this->observationPoints[$changedObservationPoint->id()->toString()] = $changedObservationPoint;

        return $this->self();
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): WellDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getOp(ObservationPointId::fromString($this->boundaryId->toString()));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof WellDateTimeValue){
            return $value;
        }

        #return null;
        return WellDateTimeValue::fromParams($dateTime, 0);
    }

    protected function self(): ModflowBoundary
    {
        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->affectedLayers, $this->metadata);
        $self->activeCells = $this->activeCells;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }
}
