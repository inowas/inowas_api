<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Id\ObservationPointId;

class WellBoundary extends ModflowBoundary
{
    const CARDINALITY = '1';
    const TYPE = 'wel';

    public function addPumpingRate(WellDateTimeValue $pumpingRate): WellBoundary
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
}
