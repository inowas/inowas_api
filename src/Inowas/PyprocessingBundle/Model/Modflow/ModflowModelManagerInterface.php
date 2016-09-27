<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;


use AppBundle\Entity\ModFlowModel;

interface ModflowModelInterface
{
    /**
     * @return ModFlowModel
     */
    public function create();

    /**
     * @param $id
     * @return ModFlowModel
     */
    public function findById($id);

    /**
     * @param ModFlowModel $model
     * @param bool $calculate
     * @return ModFlowModel
     */
    public function update(ModFlowModel $model, $calculate = false);

    /**
     * @param ModFlowModel $model
     */
    public function persist(ModFlowModel $model);

    /**
     * @param ModFlowModel $model
     */
    public function remove(ModFlowModel $model);
}