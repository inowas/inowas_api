<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class GeologicalUnitRestController extends FOSRestController
{
    /**
     * Return a geological unit by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a geological unit by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id geologicalUnit-Id
     *
     * @return View
     */
    public function getGeologicalunitAction($id)
    {
        $geologicalUnit = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalUnit')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$geologicalUnit)
        {
            throw $this->createNotFoundException('Geological unit with id='.$id.' not found.');
        }

        if ($geologicalUnit->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $geologicalUnit->getOwner())
        {
            $view = View::create();
            $view->setData($geologicalUnit)
                ->setStatusCode(200)
                ->setSerializationContext(SerializationContext::create()->enableMaxDepthChecks())
            ;
            return $view;
        } else
        {
            throw $this->createAccessDeniedException();
        }
    }
}
