<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class DisPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){
        return new DisPackage();
    }
}