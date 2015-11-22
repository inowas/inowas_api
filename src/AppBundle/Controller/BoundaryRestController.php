<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class BoundaryRestController extends FOSRestController
{
    /**
     * Return a boundary by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a boundary by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id boundary-id
     *
     * @return View
     */
    public function getBoundaryAction($id)
    {
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:Boundary')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$entity) {
            throw $this->createNotFoundException('Not found.');
        }

        $view = View::create();
        $view->setData($entity)->setStatusCode(200);

        return $view;
    }
}
