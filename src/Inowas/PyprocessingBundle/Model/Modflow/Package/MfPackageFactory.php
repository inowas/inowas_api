<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class MfPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){
        return new MfPackage($model->getId()->toString(), 'mf2005', 'mf2005');
    }
}