<?php

namespace Inowas\ModflowBundle\Service;

use Inowas\Flopy\Model\Factory\CalculationPropertiesFactory;
use Inowas\Flopy\Model\Factory\PackageFactory;
use Inowas\Flopy\Model\Package\CalculationProperties;
use Inowas\Flopy\Model\Package\PackageInterface;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class FlopyPackageManager
{
    /**
     * @param ModflowModel $model
     * @return CalculationProperties
     */
    public function getCalculationProperties(ModflowModel $model): CalculationProperties {
        return CalculationPropertiesFactory::loadFromApiRunAndSubmit($model);
    }

    /**
     * @param ModflowModel $model
     * @param Soilmodel $soilmodel
     * @param string $packageName
     * @return PackageInterface
     */
    public function getPackageData(ModflowModel $model, Soilmodel $soilmodel, string $packageName):PackageInterface {
        return PackageFactory::create($packageName, $model, $soilmodel);
    }
}
