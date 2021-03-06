<?php

namespace Inowas\ModflowModel\Model\Exception;

use Inowas\Common\Id\ModflowId;

final class ModflowCalculationNotFoundException extends \InvalidArgumentException
{
    public static function withId(ModflowId $calculationId)
    {
        return new self(sprintf('ModflowCalculation with id %s cannot be found.', $calculationId->toString()));
    }
}
