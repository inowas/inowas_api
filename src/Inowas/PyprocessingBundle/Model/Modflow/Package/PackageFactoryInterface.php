<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;


use AppBundle\Entity\ModFlowModel;

interface PackageFactoryInterface
{

    public function create(ModFlowModel $model);

}