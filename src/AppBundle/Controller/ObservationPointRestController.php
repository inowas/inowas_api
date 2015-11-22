<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ObservationPointRestController extends FOSRestController
{
    /**
     * Return a observationPoint by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a observationPoint by id",
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
    public function getObservationpointAction($id)
    {
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:ObservationPoint')
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
