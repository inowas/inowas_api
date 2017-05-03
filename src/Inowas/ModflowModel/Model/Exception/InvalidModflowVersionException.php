<?php

namespace Inowas\ModflowModel\Model\Exception;

final class InvalidModflowVersionException extends \InvalidArgumentException
{
    public static function withVersion($version, array $availableVersions) {
        return new self(sprintf('The given version %s is not valid. Available versions are %s', $version, implode(",",$availableVersions)));
    }
}
