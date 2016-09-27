<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use AppBundle\Entity\ModFlowModel;

interface ModflowModelManagerInterface extends ModflowManagerInterface
{
    public function remove(ModFlowModel $model);

    public function update(ModFlowModel $model, $calculate = false);

    public function persist(ModFlowModel $model);
}