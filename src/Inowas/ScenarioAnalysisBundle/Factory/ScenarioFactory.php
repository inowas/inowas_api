<?php

namespace Inowas\ScenarioAnalysisBundle\Factory;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;

class ScenarioFactory
{
    final private function __construct(){}

    public static function create(ModflowModel $model){
        return new Scenario($model);
    }
}