<?php

namespace Inowas\Flopy\Model\Factory;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\SoilmodelBundle\Model\Soilmodel;

interface PackageFactoryInterface
{
    /**
     * @param ModFlowModel $model
     * @param Soilmodel $soilmodel
     */
    public function create(ModflowModel $model, Soilmodel $soilmodel);
}
