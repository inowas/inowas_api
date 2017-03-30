<?php

namespace Inowas\Modflow\Model\Exception;

use Inowas\Soilmodel\Model\SoilmodelId;

final class SoilmodelNotFoundException extends \InvalidArgumentException
{
    public static function withSoilmodelId(SoilmodelId $id)
    {
        return new self(sprintf('Soilmodel with id %s cannot be found.', $id->toString()));
    }
}
