<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class BoundaryRestController extends FOSRestController
{
    /**
     * Return the boundary list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the boundary list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserBoundariesAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (!$user)
        {
            throw $this->createNotFoundException('User with username '.$username.' not found.');
        }

        $boundaries = $this->getDoctrine()
            ->getRepository('AppBundle:Boundary')
            ->findBy(
                array('owner' => $user),
                array('id' => 'ASC')
            )
        ;

        $view = View::create();
        $view->setData($boundaries)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('modelobjectlist'))
                ->setSerializeNull(true)
                ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }

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
    public function getBoundariesAction($id)
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
            $view->setData($boundary)
                ->setStatusCode(200)
                ->setSerializationContext(SerializationContext::create()
                    ->setGroups(array('modelobjectdetails'))
                    ->enableMaxDepthChecks()
                );

            return $view;
        } else
        {
            throw $this->createAccessDeniedException();
        }
    }
}
