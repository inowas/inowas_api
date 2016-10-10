<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

class ObservationPointRestController extends FOSRestController
{
    /**
     * Return a list of observationPoints by username
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a list of observationPoints by username",
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
    public function getUserObservationpointsAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (! $user instanceof User) {
            throw  $this->createNotFoundException(sprintf('User with username %s not found.', $username));
        }

        $entities = $this->getDoctrine()
            ->getRepository('AppBundle:ObservationPoint')
            ->findBy(
                array('owner' => $user),
                array('id' => 'ASC')
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
     * Return an observationPoint by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an observationPoint by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id
     *
     * @return View
     */
    public function getObservationpointsAction($id)
    {

        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('ObservationPoint with Id %s not found.', $id));
        }

        /** @var ObservationPoint $entity */
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:ObservationPoint')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (! $entity instanceof ObservationPoint){
            throw $this->createNotFoundException(sprintf('ObservationPoint with Id %s not found.', $id));
        }

        $view = View::create();
        $view->setData($entity)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('modelobjectdetails')));

        return $view;
    }
}
