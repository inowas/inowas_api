<?php

declare(strict_types=1);

namespace Inowas\Common\Exception;

class InvalidArrayDimensionException extends \InvalidArgumentException
{
    public static function withExpectedDimensionAndValue(int $expected, $value): InvalidArrayDimensionException
    {
        return new self(sprintf('The expected array Dimension is %s, %s given.', $expected, $value));
    }
}
