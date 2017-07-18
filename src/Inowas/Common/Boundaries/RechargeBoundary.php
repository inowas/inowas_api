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

        $this->addDateTimeValue($rechargeRate, $observationPointId);
        return $this->self();
    }

    public function findValueByDateTime(DateTime $dateTime): RechargeDateTimeValue
    {
        /** @var ObservationPoint $op */
        $observationPointId = ObservationPointId::fromString('OP');
        $op = $this->getObservationPoint($observationPointId);
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof RechargeDateTimeValue){
            return $value;
        }

        return RechargeDateTimeValue::fromParams($dateTime, 0);
    }
}
