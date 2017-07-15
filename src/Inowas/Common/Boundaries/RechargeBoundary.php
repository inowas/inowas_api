<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Modflow\Name;

class RechargeBoundary extends ModflowBoundary
{
    const CARDINALITY = '1';
    const TYPE = 'rch';

    /** @var  ObservationPoint */
    protected $observationPoint;

    public function addRecharge(RechargeDateTimeValue $rechargeRate): ModflowBoundary
    {
        $observationPointId = ObservationPointId::fromInt(0);
        if (! $this->hasObservationPoint($observationPointId)) {
            $this->addObservationPoint(
                ObservationPoint::fromTypeNameAndGeometry(
                    $this->type(),
                    Name::fromString($this->name->toString()),
                    $this->geometry->getPointFromGeometry()
                )
            );
        }

        $this->addDateTimeValue($rechargeRate, $observationPointId);
        return $this->self();
    }

    public function findValueByDateTime(DateTime $dateTime): RechargeDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getObservationPoint(ObservationPointId::fromInt(0));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof RechargeDateTimeValue){
            return $value;
        }

        return RechargeDateTimeValue::fromParams($dateTime, 0);
    }
}
