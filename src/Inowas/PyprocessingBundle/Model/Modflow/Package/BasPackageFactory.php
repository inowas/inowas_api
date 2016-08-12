<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class BasPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){
        return new BasPackage();
    }
}