<?php

namespace Inowas\ModflowBundle\Model\Factory;


use AppBundle\Entity\ModFlowModel;

interface PackageFactoryInterface
{
    /**
     * @param ModFlowModel $model
     * @return mixed
     */
    public function create(ModFlowModel $model);
}
