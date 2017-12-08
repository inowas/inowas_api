<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Id\ObservationPointId;

class ConstantHeadBoundary extends ModflowBoundary
{
    public const CARDINALITY = 'n';
    public const TYPE = 'chd';

    public function addConstantHeadToObservationPoint(ObservationPointId $observationPointId, ConstantHeadDateTimeValue $chdTimeValue): ModflowBoundary
    {
        $this->addDateTimeValue($chdTimeValue, $observationPointId);
        return $this->self();
    }
}
