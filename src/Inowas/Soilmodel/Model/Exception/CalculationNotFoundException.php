<?php

namespace Inowas\Soilmodel\Model\Exception;

use Inowas\Common\Id\ModflowId;

final class CalculationNotFoundException extends \InvalidArgumentException
{
    public static function withId(ModflowId $id): CalculationNotFoundException
    {
        return new self(sprintf('Calculation with id %s cannot be found.', $id->toString()));
    }
}
