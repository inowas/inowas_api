<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use AppBundle\Entity\ModFlowModel;

interface ModflowModelManagerInterface
{
    public function findById($id);

    public function create();

    public function remove(ModFlowModel $model);

    public function update(ModFlowModel $model, $calculate = false);

    public function persist(ModFlowModel $model);
}
