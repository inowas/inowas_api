<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AreaRestController extends FOSRestController
{
    /**
     * Return an area by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an area by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id area-id
     *
     * @return View
     */
    public function getAreaAction($id)
    {
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:Area')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$entity) {
            throw $this->createNotFoundException('Not found.');
        }

        $view = View::create();
        $view->setData($entity)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()->enableMaxDepthChecks())
        ;

        return $view;
    }
}
