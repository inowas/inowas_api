<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event\Boundary;

use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\Event\Boundary\BoundaryObservationPointWasAdded;

/** @noinspection LongInheritanceChainInspection */
class BoundaryObservationPointWasUpdated extends BoundaryObservationPointWasAdded
{
    public static function updatedByUserWithData(
        BoundaryId $boundaryId,
        UserId $userId,
        ObservationPoint $observationPoint
    ): BoundaryObservationPointWasUpdated
    {
        $event = self::occur(
            $boundaryId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'observationpoint' => serialize($observationPoint)
            ]
        );

        return $event;
    }
}
