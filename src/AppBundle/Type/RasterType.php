<?php

namespace AppBundle\Type;

use AppBundle\Model\Raster;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class RasterType extends Type
{

    const RASTER = 'raster';

    public function getName()
    {
        return self::RASTER;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'raster';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /**
         * ToDo: Finish implementing it
         */
        return new \AppBundle\Model\Raster();
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Raster)
        {
            //return sprintf('%d, %d, %f, %f, %f, %f, %f, %f, %d', 10, 10, 0.1, 0.1, 0.1, 0.1, 0, 0, 4269);
        }
    }

    public function canRequireSQLConversion()
    {
        return true;
    }

    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        /**
         * ToDo: Finish implementing it
         */
        return sprintf('AsText(%s)', $sqlExpr);
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return $sqlExpr;
    }

}