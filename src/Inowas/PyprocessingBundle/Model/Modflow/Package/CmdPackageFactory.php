<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class CmdPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

        $cmd = new CmdPackage();
        $adapter = new CmdPackageAdapter($model);

        $cmd->setPackages($adapter->getPackages());
        $cmd->setWriteInput(true);
        $cmd->setRun(true);

        return $cmd;
    }
}