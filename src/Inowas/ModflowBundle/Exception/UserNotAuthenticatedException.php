<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Exception;

class UserNotAuthenticatedException extends \Exception
{
    public static function withMessage(string $message): UserNotAuthenticatedException
    {
        return new self($message, 304);
    }
}
