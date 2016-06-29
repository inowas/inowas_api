<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

class GeologicalLayerRestController extends FOSRestController
{
    /**
     * Return a list of geological layers by username
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a list of geological layers by username",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $username username
     *
     * @return View
     */
    public function getUserGeologicallayersAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (! $user instanceof User) {
            throw  $this->createNotFoundException('User with username='.$username.' not found.');
        }

        $entities = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findBy(
                array('owner' => $user),
                array('order' => 'ASC')
            )
        ;

        $view = View::create();
        $view->setData($entities)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups('modelobjectlist')
            )
        ;

        return $view;
    }

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
     * @param string $id GeologicalLayer-Id
     *
     * @return View
     */
    public function getGeologicallayersAction($id)
    {

        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('GeologicalPoint with id='.$id.' not found.');
        }

        /** @var GeologicalLayer $geologicalLayers */
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (! $entity instanceof GeologicalLayer) {
            throw $this->createNotFoundException('Layer with id='.$id.' not found.');
        }

        $view = View::create();
        $view->setData($entity)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('modelobjectdetails')));

        return $view;
    }
}
