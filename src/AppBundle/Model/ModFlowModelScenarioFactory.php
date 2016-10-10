<?php

namespace AppBundle\Model;


use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\ModflowModelScenario;

class ModFlowModelScenarioFactory
{

    private final function __construct(){}

    /**
     * @param ModFlowModel $model
     * @return ModflowModelScenario
     */
    public static function create(ModFlowModel $model)
    {
        return new ModflowModelScenario($model);
    }
}
