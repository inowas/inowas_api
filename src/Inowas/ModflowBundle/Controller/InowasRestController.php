<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
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
}
