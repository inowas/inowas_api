<?php

namespace AppBundle\Type;

use AppBundle\Model\Interpolation\BoundingBox;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Type that maps an SQL VARCHAR to a PHP string.
 *
 * @since 2.0
 */
class BoundingBoxType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getJsonTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        /** @var BoundingBox $value */
        $bb = array();
        $bb['x_min'] = $value->getXMin();
        $bb['x_max'] = $value->getXMax();
        $bb['y_min'] = $value->getYMin();
        $bb['y_max'] = $value->getYMax();
        $bb['srid'] = $value->getSrid();
        return json_encode($bb);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return array();
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;
        $bb = json_decode($value, true);
        return new BoundingBox($bb['x_min'], $bb['x_max'], $bb['y_min'], $bb['y_max'], $bb['srid']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bounding_box';
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return ! $platform->hasNativeJsonType();
    }
}
