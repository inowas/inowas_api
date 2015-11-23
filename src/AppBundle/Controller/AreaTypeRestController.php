<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AreaTypeRestController extends FOSRestController
{
    /**
     * Return overall list of area types
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return overall list of area types",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function getAreatypesAction()
    {
        $entities = $this->getDoctrine()
            ->getRepository('AppBundle:AreaType')
            ->findAll()
        ;

        if (!$entities) {
            throw $this->createNotFoundException('Not found.');
        }

        $view = View::create();
        $view->setData($entities)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()->enableMaxDepthChecks())
        ;

        return $view;
    }
}