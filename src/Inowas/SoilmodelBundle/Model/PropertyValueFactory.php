<?php

namespace Inowas\Soilmodel\Model;


class PropertyValueFactory
{
    final private function __construct(){}

    public static function createFromValue($value)
    {
        return PropertyValue::fromValue($value);
    }
}
