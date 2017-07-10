<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class WellBoundary extends AbstractBoundary
{
    const CARDINALITY = '1';
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

    public function addPumpingRate(WellDateTimeValue $pumpingRate): ModflowBoundary
    {
        // In case of well, the observationPointId is the boundaryId
        $observationPointId = ObservationPointId::fromString($this->boundaryId->toString());

        if (! $this->hasObservationPoint($observationPointId)) {
            $this->addObservationPoint(
                ObservationPoint::fromIdTypeNameAndGeometry(
                    ObservationPointId::fromString($this->boundaryId->toString()),
                    $this->type(),
                    ObservationPointName::fromString($this->name->toString()),
                    $this->geometry->getPointFromGeometry()
                )
            );
        }

        $this->addDateTimeValue($pumpingRate, $observationPointId);
        return $this->self();
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): WellDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getObservationPoint(ObservationPointId::fromString($this->boundaryId->toString()));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof WellDateTimeValue){
            return $value;
        }

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
