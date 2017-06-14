<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Exception;

class AccessDeniedException extends \Exception
{
    public static function withMessage(string $message): AccessDeniedException
    {
        return new self($message, 403);
    }
}
