<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

class GeologicalUnitRestController extends FOSRestController
{
    /**
     * Return a list of geological unit by username
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a list of geological units by username",
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
    public function getUserGeologicalunitsAction($username)
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
            ->getRepository('AppBundle:GeologicalUnit')
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
     * @param string $id
     *
     * @return View
     */
    public function getGeologicalunitsAction($id)
    {

        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('GeologicalUnit with id='.$id.' not found.');
        }

        /** @var GeologicalUnit $entity */
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalUnit')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (! $entity instanceof GeologicalUnit) {
            throw $this->createNotFoundException('GeologicalUnit with id='.$id.' not found.');
        }

        $view = View::create();
        $view->setData($entity)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('modelobjectdetails')));

        return $view;
    }
}
