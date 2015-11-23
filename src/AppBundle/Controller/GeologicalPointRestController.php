<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class GeologicalPointRestController extends FOSRestController
{
    /**
     * Return a geological point by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a geological point by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id geologicalPoint-Id
     *
     * @return View
     */
    public function getGeologicalpointAction($id)
    {
        $geologicalPoint = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalPoint')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$geologicalPoint)
        {
            throw $this->createNotFoundException('Geological point with id='.$id.' not found.');
        }

        if ($geologicalPoint->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $geologicalPoint->getOwner())
        {
            $view = View::create();
            $view->setData($geologicalPoint)
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
