<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Modflow\Name;

class WellBoundary extends ModflowBoundary
{
    const CARDINALITY = '1';
    const TYPE = 'wel';

    public function addPumpingRate(WellDateTimeValue $pumpingRate): WellBoundary
    {
        $observationPointId = ObservationPointId::fromString('OP');
        if (! $this->hasObservationPoint($observationPointId)) {
            $this->addObservationPoint(
                ObservationPoint::fromIdTypeNameAndGeometry(
                    $observationPointId,
                    $this->type(),
                    Name::fromString($this->name->toString()),
                    $this->geometry->getPointFromGeometry()
                )
            );
        }

        $this->addDateTimeValue($pumpingRate, $observationPointId);
        return $this->self();
    }

    public function findValueByDateTime(DateTime $dateTime): WellDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getObservationPoint(ObservationPointId::fromString('OP'));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof WellDateTimeValue){
            return $value;
        }

        return WellDateTimeValue::fromParams($dateTime, 0);
    }
}
