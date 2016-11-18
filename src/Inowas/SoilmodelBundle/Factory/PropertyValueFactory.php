<?php

namespace Inowas\SoilmodelBundle\Factory;

use Inowas\SoilmodelBundle\Model\PropertyValue;

class PropertyValueFactory
{
    final private function __construct(){}

    public static function createFromValue($value)
    {
        return PropertyValue::fromValue($value);
    }
}
