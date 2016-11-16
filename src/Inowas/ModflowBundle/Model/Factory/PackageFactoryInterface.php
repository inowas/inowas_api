<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\Soilmodel\Model\Soilmodel;

interface PackageFactoryInterface
{
    /**
     * @param ModFlowModel $model
     * @param Soilmodel $soilmodel
     */
    public function create(ModflowModel $model, Soilmodel $soilmodel);
}
