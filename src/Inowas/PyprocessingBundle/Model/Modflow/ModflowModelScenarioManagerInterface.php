<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use AppBundle\Entity\ModflowModelScenario;

interface ModflowModelScenarioManagerInterface extends ModflowManagerInterface
{
    public function remove(ModflowModelScenario $model);

    public function update(ModflowModelScenario $model);

    public function persist(ModflowModelScenario $model);
}