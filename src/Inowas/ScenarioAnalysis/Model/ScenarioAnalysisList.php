<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model;

interface ScenarioAnalysisList
{
    public function add(ScenarioAnalysisAggregate $scenarioAnalysis);

    public function get(ScenarioAnalysisId $scenarioAnalysisId);
}
