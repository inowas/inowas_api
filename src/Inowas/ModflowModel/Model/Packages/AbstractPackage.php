<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

abstract class AbstractPackage implements PackageInterface
{
    public const TYPE = 'abs';
    public const DESCRIPTION = 'Abstract Package';

    public static function type(): string
    {
        return static::TYPE;
    }

    public static function description(): string
    {
        return static::DESCRIPTION;
    }
}
