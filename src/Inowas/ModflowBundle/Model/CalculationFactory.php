<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\Flopy\Model\Package\CalculationProperties;

class CalculationFactory
{

    final private function __construct(){}

    public static function create(CalculationProperties $calculationProperties, ModflowModel $model){
        $calculation = new Calculation($calculationProperties);
        $calculation->setModelId($model->getId());
        return $calculation;
    }
}
