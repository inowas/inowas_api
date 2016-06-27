<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BoundaryModelObject;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @throws NotFoundHttpException
     */
    public function getBoundariesAction($id)
    {
        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Boundary with id='.$id.' not found.');
        }

        $boundary = $this->getDoctrine()
            ->getRepository('AppBundle:ModelObject')
            ->findOneBy(array(
                'id' => $id
            ))
        ;

        if (!$boundary instanceof BoundaryModelObject) {
            throw $this->createNotFoundException('Boundary with id='.$id.' not found.');
        }

        $view = View::create();
        $view->setData($boundary)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('modelobjectdetails'))
                ->enableMaxDepthChecks()
            );

        return $view;
    }
}
