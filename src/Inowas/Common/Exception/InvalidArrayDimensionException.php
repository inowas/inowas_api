<?php

declare(strict_types=1);

namespace Inowas\Common\Exception;

class InvalidArrayDimensionException extends \InvalidArgumentException
{
    public static function withExpectedDimensionAndValue(int $expected): InvalidArrayDimensionException
    {
        return new self(sprintf('The expected array Dimension is %s.', $expected));
    }
}
