<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Exception;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function withMessage(string $message): InvalidArgumentException
    {
        return new self($message, 422);
    }
}
