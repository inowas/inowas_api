<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Id\ObservationPointId;

class RechargeBoundary extends ModflowBoundary
{
    const CARDINALITY = '1';
    const TYPE = 'rch';

    /** @var  ObservationPoint */
    protected $observationPoint;

    public function addRecharge(RechargeDateTimeValue $rechargeRate): ModflowBoundary
    {
        // In case of rechargeBoundary, the observationPointId is the boundaryId
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

        $this->addDateTimeValue($rechargeRate, $observationPointId);
        return $this->self();
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): RechargeDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getObservationPoint(ObservationPointId::fromString($this->boundaryId->toString()));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof RechargeDateTimeValue){
            return $value;
        }

        return RechargeDateTimeValue::fromParams($dateTime, 0);
    }
}
