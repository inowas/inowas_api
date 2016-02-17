<?php

namespace AppBundle\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class SimpleRaster extends Type
{

    const SIMPLE_RASTER = 'simple_raster';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'simple_raster';
    }

    public function getName()
    {
        return self::SIMPLE_RASTER;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = str_replace('{', '[', $value);
        $value = str_replace('}', ']', $value);

        $value = json_decode($value);

        return $value;
    }

    public function convertToDatabaseValue($rows, AbstractPlatform $platform)
    {
        $value = null;

        if (is_array($rows))
        {
            $value .= '{';
            foreach ($rows as $row)
            {
                $value .= '{';
                $value .= sprintf('%s', implode(",", $row));
                $value .= '},';
            }
            $value = rtrim($value, ",");
            $value .= '}';
        }

        return $value;
    }
}