<?php

namespace AppBundle\Model;

use AppBundle\Entity\ConstantHeadBoundary;

class ConstantHeadBoundaryFactory
{
    private final function __construct(){}

    /**
     * @return ConstantHeadBoundary
     */
    public static function create()
    {
        return new ConstantHeadBoundary();
    }
}
