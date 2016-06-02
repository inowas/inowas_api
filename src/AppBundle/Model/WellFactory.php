<?php

namespace AppBundle\Model;

use AppBundle\Entity\Well;

class WellFactory
{
    public static function create()
    {
        return new Well();
    }
}