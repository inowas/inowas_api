<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

class GeologicalPointRestController extends FOSRestController
{
    /**
     * Return a list of geological points by username
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a list of geological points by username",
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
    public function getUserGeologicalpointsAction($username)
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
            ->getRepository('AppBundle:GeologicalPoint')
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
     * @param string $id
     *
     * @return View
     */
    public function getGeologicalpointsAction($id)
    {

        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('GeologicalPoint with id='.$id.' not found.');
        }

        /** @var GeologicalPoint $entity */
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalPoint')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (! $entity instanceof GeologicalPoint) {
            throw $this->createNotFoundException('GeologicalPoint with id='.$id.' not found.');
        }

        $view = View::create();
        $view->setData($entity)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('modelobjectdetails')));

        return $view;
    }
}
