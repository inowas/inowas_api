<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @throws NotFoundHttpException
     */
    public function getUserAreasAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (! $user instanceof User) {
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
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function getAreaAction($id)
    {
        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Area with id='.$id.' not found.');
        }

        $area = $this->getDoctrine()
            ->getRepository('AppBundle:Area')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (! $area instanceof Area) {
            throw $this->createNotFoundException('Area with id='.$id.' not found.');
        }

        $area->setSurface(
            $this->getDoctrine()
            ->getRepository('AppBundle:Area')
            ->getAreaSurfaceById($area->getId())
        );

        $view = View::create();
        $view->setData($area)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('modelobjectdetails')));

        return $view;
    }
}
