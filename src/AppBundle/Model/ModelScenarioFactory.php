<?php

namespace AppBundle\Model;


use AppBundle\Entity\AbstractModel;
use AppBundle\Entity\ModelScenario;

class ModelScenarioFactory
{
    public static function create(AbstractModel $model)
    {
        return new ModelScenario($model);
    }
}