<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeneralHeadBoundary;

class GeneralHeadBoundaryFactory
{

    private final function __construct(){}

    /**
     * @return GeneralHeadBoundary
     */
    public static function create()
    {
        return new GeneralHeadBoundary();
    }
}
