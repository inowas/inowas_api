<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class InowasJsonInvalidUuidResponse extends JsonResponse
{
    public static function withId(string $id): InowasJsonInvalidUuidResponse
    {
        return new self(array('status' => 400, 'message' => sprintf('The given id %s in not a valid Uuid.', $id)), 400);
    }
}
