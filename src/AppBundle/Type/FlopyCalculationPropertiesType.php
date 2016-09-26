<?php

namespace AppBundle\Type;

use AppBundle\Model\ActiveCells;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ArrayType;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;

class FlopyCalculationPropertiesType extends ArrayType
{
    const NAME = 'flopy_calculation_properties';

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return FlopyCalculationProperties::fromArray(parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritDoc}
     *
     * @param $value ActiveCells
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return serialize($value->toArray());
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
