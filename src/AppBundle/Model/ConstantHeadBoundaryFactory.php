<?php

namespace AppBundle\Model;

use AppBundle\Entity\ConstantHeadBoundary;

class ConstantHeadBoundaryFactory
{
    public static function create()
    {
        return new ConstantHeadBoundary();
    }
}