<?php

namespace Inowas\ScenarioAnalysisBundle\Factory;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\ScenarioAnalysis;

class ScenarioAnalysisFactory
{
    final private function __construct(){}

    public static function create(ModflowModel $baseModel){
        return new ScenarioAnalysis($baseModel);
    }
}