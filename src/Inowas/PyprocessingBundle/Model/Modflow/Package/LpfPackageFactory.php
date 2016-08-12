<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class LpfPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){
        return new LpfPackage();
    }
}