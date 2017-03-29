<?php

namespace Inowas\Modflow\Model\Exception;

final class InvalidPackageNameException extends \InvalidArgumentException
{
    public static function withName(string $name, array $availableNames) {
        return new self(sprintf('The given PackageName %s is not valid. Available names are %s', $name, implode(",",$availableNames)));
    }
}
