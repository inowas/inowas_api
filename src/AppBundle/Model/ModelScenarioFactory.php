<?php

namespace AppBundle\Model;


use AppBundle\Entity\AbstractModel;
use AppBundle\Entity\ModelScenario;

class ModelScenarioFactory
{
    private final function __construct(){}

    /**
     * @param AbstractModel $model
     * @return ModelScenario
     */
    public static function create(AbstractModel $model)
    {
        return new ModelScenario($model);
    }
}
