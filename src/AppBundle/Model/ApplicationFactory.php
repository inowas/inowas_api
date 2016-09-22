<?php

namespace AppBundle\Model;

use AppBundle\Entity\Application;

class ApplicationFactory
{
    private final function __construct(){}

    /**
     * @return Application
     */
    public static function create()
    {
        return new Application();
    }
}
