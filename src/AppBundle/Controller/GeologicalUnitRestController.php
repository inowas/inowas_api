<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalUnit;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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

        if (!$user)
        {
            throw  $this->createNotFoundException('User with username='.$username.' not found.');
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
        /** @var GeologicalUnit $entity */
        $entity = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalUnit')
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
