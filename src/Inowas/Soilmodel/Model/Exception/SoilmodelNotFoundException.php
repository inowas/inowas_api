<?php

namespace Inowas\Soilmodel\Model\Exception;

use Inowas\Soilmodel\Model\SoilmodelId;

final class SoilmodelNotFoundException extends \InvalidArgumentException
{
    public static function withSoilModelId(SoilmodelId $id): SoilmodelNotFoundException
    {
        return new self(sprintf('Soilmodel with id %s cannot be found.', $id->toString()));
    }
}
