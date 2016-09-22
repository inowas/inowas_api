<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;

class PackageFactory
{
    private function __construct(){}

    public static function create($packageName, ModFlowModel $model) {

        $packageFactory = "Inowas\\PyprocessingBundle\\Model\\Modflow\\Package\\".ucfirst(strtolower($packageName))."PackageFactory";
        if(! class_exists($packageFactory)){
            throw new InvalidArgumentException(sprintf('Invalid packageName %s', $packageName));
        }

        $packageFactory = new $packageFactory;

        if (! $packageFactory instanceof PackageFactoryInterface) {
            throw new InvalidArgumentException(sprintf('Class %s has not implemented the PackageFactoryInterface', $packageFactory));
        }

        return $packageFactory->create($model);
    }
}
