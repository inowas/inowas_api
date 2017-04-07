<?php

declare(strict_types=1);

namespace Inowas\Common\Exception;

use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class ObservationPointNotFoundInBoundaryException extends \InvalidArgumentException
{
    public static function withIds(BoundaryId $boundaryId, ObservationPointId $observationPointId): ObservationPointNotFoundInBoundaryException
    {
        return new self(sprintf(
            'The ObservationPoint with Id %s cannot be found in Boundary with id.', $observationPointId, $boundaryId
        ));
    }
}
