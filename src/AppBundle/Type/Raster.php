<?php

namespace AppBundle\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Raster extends Type
{

    const RASTER = 'raster';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'raster';
    }

    public function getName()
    {
        return self::RASTER;
    }

    public function canRequireSQLConversion()
    {
        return true;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = str_replace('{', '[', $value);
        $value = str_replace('}', ']', $value);

        $value = json_decode($value);

        return $value;
    }

    public function convertToDatabaseValue($sqlExpr, AbstractPlatform $platform)
    {
        return $sqlExpr;
    }

}