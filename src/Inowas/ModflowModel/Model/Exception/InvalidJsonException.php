<?php

namespace Inowas\ModflowModel\Model\Exception;

final class InvalidJsonException extends \InvalidArgumentException
{
    public static function withoutContent(): InvalidJsonException
    {
        return new self('Invalid Json-String.');
    }
}
