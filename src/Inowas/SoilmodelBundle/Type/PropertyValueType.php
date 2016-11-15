<?php

namespace Inowas\Soilmodel\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;
use Inowas\Soilmodel\Model\PropertyType;
use Inowas\Soilmodel\Model\PropertyValueFactory;

/**
 * Type that maps an SQL VARCHAR to a PHP string.
 *
 * @since 2.0
 */
class PropertyValueType extends JsonArrayType
{
    const NAME = 'soilmodel_property_value';

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        return PropertyValueFactory::createFromValue(json_decode($value, true));
    }

    /**
     * {@inheritDoc}
     *
     * @param $value PropertyType
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        return json_encode($value);
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
