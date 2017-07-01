<?php

namespace Inowas\ModflowModel\Model\Exception;

use Inowas\Common\Id\BoundaryId;

final class ModflowBoundaryNotFoundException extends \InvalidArgumentException
{
    public static function withId(BoundaryId $boundaryId): ModflowBoundaryNotFoundException
    {
        return new self(sprintf('Boundary with id %s cannot be found.', $boundaryId->toString()));
    }
}
