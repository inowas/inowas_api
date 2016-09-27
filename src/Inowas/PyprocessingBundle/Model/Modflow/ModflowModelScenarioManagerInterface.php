<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\ModflowModelScenario;

interface ModflowModelScenarioManagerInterface
{
    public function findById($id);

    public function create(ModFlowModel $model);

    public function remove(ModflowModelScenario $model);

    public function update(ModflowModelScenario $model);

    public function persist(ModflowModelScenario $model);
}
