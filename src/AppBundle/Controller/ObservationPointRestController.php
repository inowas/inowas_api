<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ObservationPoint;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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

        if (!$user)
        {
            throw  $this->createNotFoundException('User with username='.$username.' not found.');
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
        /** @var ObservationPoint $entity */
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:ObservationPoint')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($entity->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $entity->getOwner())
        {
            $view = View::create();
            $view->setData($entity)
                ->setStatusCode(200)
                ->setSerializationContext(SerializationContext::create()
                    ->setGroups(array('modelobjectdetails')));

            return $view;
        } else
        {
            throw $this->createAccessDeniedException();
        }
    }
}
