<?php

namespace AppBundle\Model;

use AppBundle\Entity\StreamBoundary;

class StreamBoundaryFactory
{

    private final function __construct(){}

    /**
     * @return StreamBoundary
     */
    public static function create()
    {
        return new StreamBoundary();
    }
}