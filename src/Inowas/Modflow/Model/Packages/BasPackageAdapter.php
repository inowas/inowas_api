<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowModelAggregate;
use Inowas\Soilmodel\Model\SoilmodelAggregate;

class BasPackageAdapter
{
    public function __invoke(
        ModflowCalculationAggregate $calculation,
        ModflowModelAggregate $modflowModel,
        SoilmodelAggregate $soilmodel
    ){
        return DisPackage::fromParams(
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
