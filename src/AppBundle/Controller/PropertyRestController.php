<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PropertyRestController extends FOSRestController
{
    /**
     * Return a property by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an property by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id property-id
     *
     * @return View
     */
    public function getPropertyAction($id)
    {
        $property = $this->getDoctrine()
            ->getRepository('AppBundle:Property')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$property)
        {
            throw $this->createNotFoundException('Property with id='.$id.' not found.');
        }

        if ($property->getModelObject()->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $property->getModelObject()->getOwner())
        {
            $view = View::create();
            $view->setData($property)->setStatusCode(200);
            return $view;
        } else
        {
            throw $this->createAccessDeniedException();
        }
    }
}
