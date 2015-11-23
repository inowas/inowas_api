<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
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
        $boundary = $this->getDoctrine()
            ->getRepository('AppBundle:Boundary')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$boundary)
        {
            throw $this->createNotFoundException('Boundary with id='.$id.' not found.');
        }

        if ($boundary->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $boundary->getOwner())
        {
            $view = View::create();
            $view->setData($boundary)->setStatusCode(200);
            return $view;
        } else
        {
            throw $this->createAccessDeniedException();
        }
    }
}
