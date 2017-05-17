<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class InowasJsonInvalidInputResponse extends JsonResponse
{
    public static function withMessage(string $message): InowasJsonInvalidInputResponse
    {
        return new self(array('status' => 422, 'message' => $message), 422);
    }
}
