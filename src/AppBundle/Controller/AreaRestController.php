<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AreaRestController extends FOSRestController
{
    /**
     * Return the area list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the area list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserAreasAction($username)
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

        $areas = $this->getDoctrine()
            ->getRepository('AppBundle:Area')
            ->findBy(
                array('owner' => $user),
                array('dateModified' => 'ASC')
            )
        ;

        $view = View::create();
        $view->setData($areas)
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
            $view->setData($area)
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
