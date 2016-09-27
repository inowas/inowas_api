<?php

namespace AppBundle\Model;


use AppBundle\Entity\AbstractModel;
use AppBundle\Entity\ModflowModelScenario;

class ModelScenarioFactory
{
    private final function __construct(){}

    /**
     * @param AbstractModel $model
     * @return ModflowModelScenario
     */
    public static function create(AbstractModel $model)
    {
        return new ModflowModelScenario($model);
    }
}
