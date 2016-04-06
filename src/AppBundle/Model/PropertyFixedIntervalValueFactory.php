<?php

namespace AppBundle\Model;

use AppBundle\Entity\PropertyFixedIntervalValue;

class PropertyFixedIntervalValueFactory
{
    /**
     * PropertyIntervalValueFactory constructor.
     */
    public function __construct()
    {
        return new PropertyFixedIntervalValue();
    }

    /**
     * @return PropertyFixedIntervalValue
     */
    public static function create()
    {
        return new PropertyFixedIntervalValue();
    }
}