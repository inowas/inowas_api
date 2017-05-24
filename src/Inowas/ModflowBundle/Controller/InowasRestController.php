<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Inowas\AppBundle\Exception\AuthenticationException;
use Inowas\AppBundle\Model\User;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class InowasRestController extends FOSRestController
{

    protected function getContentAsArray(Request $request): array
    {
        $content = $request->getContent();
        if(empty($content)){
            return [];
        }

        return json_decode($content, true);
    }

    protected function assertUserIsLoggedInCorrectly(): void
    {
        $user = $this->getUser();
        if (! $user instanceof User){
            throw AuthenticationException::withMessage(sprintf(
                'Something went wrong with the authentication. Please check your credentials.'
            ));
        }
    }

    protected function assertContainsKey(string $key, array $content): void
    {
        if (! array_key_exists($key, $content)){
            throw InvalidArgumentException::withMessage(sprintf(
                'Expected key \'%s\' not found in submitted data. Submitted keys are: %s.', $key, implode(", ", array_keys($content))
            ));
        }
    }
}
