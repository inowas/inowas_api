<?php

namespace AppBundle\Type;

use AppBundle\Model\ActiveCells;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ArrayType;

/**
 * Type that maps an SQL VARCHAR to a PHP string.
 *
 * @since 2.0
 */
class ActiveCellsType extends ArrayType
{
    const NAME = 'active_cells';


    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        
        return ActiveCells::fromArray(parent::convertToPHPValue($value, $platform));
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
        
        return parent::convertToDatabaseValueSQL($value->toArray(), $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
