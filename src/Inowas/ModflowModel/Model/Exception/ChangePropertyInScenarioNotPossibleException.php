<?php

namespace Inowas\ModflowModel\Model\Exception;


final class ChangePropertyInScenarioNotPossibleException extends \InvalidArgumentException
{
    public static function withPropertyName(string $propertyName)
    {
        return new self(sprintf(
            'The property %s cannot be changed in a scenario. You have to change it in the baseModel.',
            $propertyName
        ));
    }
}
