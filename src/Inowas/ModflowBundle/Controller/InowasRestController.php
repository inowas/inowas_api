<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Inowas\ModflowBundle\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;

class InowasRestController extends FOSRestController
{

    protected function getContentAsArray(Request $request): array
    {
        $content = $request->getContent();
        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        return json_decode($content, true);
    }
}
