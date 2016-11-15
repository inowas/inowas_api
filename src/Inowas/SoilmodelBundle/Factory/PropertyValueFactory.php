<?php

namespace Inowas\Soilmodel\Factory;

use Inowas\Soilmodel\Model\PropertyValue;

class PropertyValueFactory
{
    final private function __construct(){}

    public static function createFromValue($value)
    {
        return PropertyValue::fromValue($value);
    }
}
