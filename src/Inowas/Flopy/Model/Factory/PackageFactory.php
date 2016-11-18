<?php

namespace Inowas\FlopyBundle\Model\Factory;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class PackageFactory
{
    private function __construct(){}

    public static function create($packageName, ModflowModel $model, Soilmodel $soilmodel) {

        $packageFactory = "Inowas\\PyprocessingBundle\\Model\\Modflow\\Package\\".ucfirst(strtolower($packageName))."PackageFactory";
        if(! class_exists($packageFactory)){
            throw new InvalidArgumentException(sprintf('Invalid packageName %s', $packageName));
        }

        $packageFactory = new $packageFactory;

        if (! $packageFactory instanceof PackageFactoryInterface) {
            throw new InvalidArgumentException(sprintf('Class %s has not implemented the PackageFactoryInterface', $packageFactory));
        }

        return $packageFactory->create($model, $soilmodel);
    }
}
