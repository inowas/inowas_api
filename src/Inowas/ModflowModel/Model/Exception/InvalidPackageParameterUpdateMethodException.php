<?php

namespace Inowas\ModflowModel\Model\Exception;

final class InvalidPackageParameterUpdateMethodException extends \InvalidArgumentException
{
    public static function withName(string $packageName, $expectedMethod) {
        return new self(sprintf('The Package with Name %s does not have a function called %s', $packageName, $expectedMethod));
    }
}
