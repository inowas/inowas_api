<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class InowasJsonWriteAccessDeniedResponse extends JsonResponse
{
    public static function withMessage(string $message): InowasJsonWriteAccessDeniedResponse
    {
        return new self(array('status' => 403, 'message' => $message), 403);
    }
}
