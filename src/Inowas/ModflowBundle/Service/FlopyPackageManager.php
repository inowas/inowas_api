<?php

namespace Inowas\FlopyBundle\Service;

use Inowas\FlopyBundle\Model\Factory\CalculationPropertiesFactory;
use Inowas\FlopyBundle\Model\Factory\PackageFactory;
use Inowas\FlopyBundle\Model\Package\CalculationProperties;
use Inowas\FlopyBundle\Model\Package\PackageInterface;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class FlopyPackageManager
{
    /**
     * @param ModflowModel $model
     * @return CalculationProperties
     */
    public function getCalculationProperties(ModflowModel $model){
        return CalculationPropertiesFactory::loadFromApiAndRun($model);
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