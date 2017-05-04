<?php

namespace Inowas\Soilmodel\Model\Exception;

use Inowas\Common\Soilmodel\SoilmodelId;

final class SoilmodelNotFoundException extends \InvalidArgumentException
{
    public static function withSoilModelId(SoilmodelId $id): SoilmodelNotFoundException
    {
        return new self(sprintf('Soilmodel with id %s cannot be found.', $id->toString()));
    }
}
