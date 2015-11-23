<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class GeologicalLayerRestController extends FOSRestController
{
    /**
     * Return a geological layer by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a geological layer by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id geologicalLayer-Id
     *
     * @return View
     */
    public function getGeologicallayerAction($id)
    {
        $geologicalLayer = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$geologicalLayer)
        {
            throw $this->createNotFoundException('Geological layer with id='.$id.' not found.');
        }

        if ($geologicalLayer->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $geologicalLayer->getOwner())
        {
            $view = View::create();
            $view->setData($geologicalLayer)->setStatusCode(200);
            return $view;
        } else
        {
            throw $this->createAccessDeniedException();
        }
    }
}
