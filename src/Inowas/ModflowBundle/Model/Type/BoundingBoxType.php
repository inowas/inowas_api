<?php

namespace Inowas\ModflowBundle\Model\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;
use Inowas\ModflowBundle\Model\BoundingBox;

/**
 * Type that maps an SQL VARCHAR to a PHP string.
 *
 * @since 2.0
 */
class BoundingBoxType extends JsonArrayType
{

    const NAME = 'bounding_box';

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
        $bb['dx_m'] = $value->getDXInMeters();
        $bb['dy_m'] = $value->getDYInMeters();
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
        $boundingBox = new BoundingBox($bb['x_min'], $bb['x_max'], $bb['y_min'], $bb['y_max'], $bb['srid']);
        $boundingBox->setDXInMeters($bb['dx_m']);
        $boundingBox->setDYInMeters($bb['dy_m']);
        return $boundingBox;
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
