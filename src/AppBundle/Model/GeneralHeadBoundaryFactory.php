<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeneralHeadBoundary;

class GeneralHeadBoundaryFactory
{
    public static function create()
    {
        return new GeneralHeadBoundary();
    }

}