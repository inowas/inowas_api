<?php

namespace Inowas\Soilmodel\Model\Exception;

use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\SoilmodelId;

final class PropertyNotFoundException extends \InvalidArgumentException
{
    public static function withIdentifier(string $type): PropertyNotFoundException
    {
        return new self(sprintf('Soilproperty with Identifier $s not found.', $type));
    }
}
