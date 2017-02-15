<?php

namespace Inowas\Modflow\Model\Exception;

final class InvalidPayloadException extends \InvalidArgumentException
{
    public static function withPayload($payload)
    {
        return new self('Something with the payload is wrong, the expected keys are not found.');
    }
}
