<?php
/**
 * Created by PhpStorm.
 * User: Ralf
 * Date: 21.03.16
 * Time: 21:04
 */

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
