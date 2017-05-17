<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class InowasJsonNotFoundResponse extends JsonResponse
{
    public static function withMessage(string $message): InowasJsonNotFoundResponse
    {
        return new self(array('status' => 404, 'message' => $message), 404);
    }
}
