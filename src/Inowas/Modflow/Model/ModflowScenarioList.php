<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

interface ModflowScenarioList
{
    public function add(ModflowScenario $scenario);

    public function get(ScenarioId $scenarioId);
}
