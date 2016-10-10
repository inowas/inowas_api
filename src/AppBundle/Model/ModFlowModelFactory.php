<?php

namespace AppBundle\Model;


use AppBundle\Entity\ModFlowModel;

class ModFlowModelFactory
{

    private final function __construct(){}

    /**
     * @return ModFlowModel
     */
    public static function create()
    {
        return new ModFlowModel();
    }
}
