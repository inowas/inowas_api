<?php

namespace Inowas\ScenarioAnalysisBundle\Factory;

use FOS\UserBundle\Model\UserInterface;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\ScenarioAnalysis;

class ScenarioAnalysisFactory
{
    final private function __construct(){}

    public static function create(UserInterface $user, ModflowModel $baseModel){
        return new ScenarioAnalysis($user, $baseModel);
    }
}
