<?php

namespace AppBundle\Type;

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
