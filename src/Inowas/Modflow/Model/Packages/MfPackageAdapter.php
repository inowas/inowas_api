<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Modflow\Model\ModflowModelAggregate;

class MfPackageAdapter
{
    public function __invoke(ModflowModelAggregate $modflowModel): MfPackage
    {
        return MfPackage::fromParams(
            $modflowModel->name(),
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }
}
