<?php

namespace Inowas\Soilmodel\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Inowas\Soilmodel\Model\PropertyType;

/**
 * Type that maps an SQL VARCHAR to a PHP string.
 *
 * @since 2.0
 */
class PropertyTypeType extends StringType
{
    const NAME = 'soilmodel_property_type';

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return PropertyType::fromString($value);
    }

    /**
     * {@inheritDoc}
     *
     * @param $value PropertyType
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return $value->getType();
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
