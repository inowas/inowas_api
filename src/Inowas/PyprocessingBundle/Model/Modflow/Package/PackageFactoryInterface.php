<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;


use AppBundle\Entity\ModFlowModel;

interface PackageFactoryInterface
{
    /**
     * @param ModFlowModel $model
     * @return mixed
     */
    public function create(ModFlowModel $model);
}
