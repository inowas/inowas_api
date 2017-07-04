<?php

namespace Inowas\ModflowBoundary\Model\Exception;

use Inowas\Common\Id\BoundaryId;

final class ModflowBoundaryAlreadyExistsException extends \InvalidArgumentException
{
    public static function withId(BoundaryId $boundaryId): ModflowBoundaryAlreadyExistsException
    {
        return new self(sprintf('Boundary with id %s exists already.', $boundaryId->toString()));
    }
}
