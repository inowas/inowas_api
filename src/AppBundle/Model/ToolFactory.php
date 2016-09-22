<?php

namespace AppBundle\Model;

use AppBundle\Entity\Tool;

class ToolFactory
{
    private final function __construct(){}

    /**
     * @return Tool
     */
    public static function create()
    {
        return new Tool();
    }
}
