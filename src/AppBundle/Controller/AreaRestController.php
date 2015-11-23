<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
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
        $area = $this->getDoctrine()
            ->getRepository('AppBundle:Area')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$area)
        {
            throw $this->createNotFoundException('Area with id='.$id.' not found.');
        }

        if ($area->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $area->getOwner())
        {
            $view = View::create();
            $view->setData($area)->setStatusCode(200);
            return $view;
        } else
        {
            throw $this->createAccessDeniedException();
        }
    }
}
