<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundException extends NotFoundHttpException
{
    public static function withMessage(string $message){
        return new self($message, null, 404);
    }
}
