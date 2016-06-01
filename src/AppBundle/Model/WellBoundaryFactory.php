<?php

namespace AppBundle\Model;


use AppBundle\Entity\WellBoundary;

class WellBoundaryFactory
{
    public static function create()
    {
        return new WellBoundary();
    }
}