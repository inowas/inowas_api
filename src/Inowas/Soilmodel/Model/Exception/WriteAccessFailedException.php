<?php

namespace Inowas\Soilmodel\Model\Exception;

use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\SoilmodelId;

final class WriteAccessFailedException extends \InvalidArgumentException
{
    public static function withSoilModelAndUserId(SoilmodelId $id, UserId $userId): WriteAccessFailedException
    {
        return new self(sprintf('User with id %s does not have sufficient rights to write Soilmodel with id %s.', $userId->toString(), $id->toString()));
    }
}
