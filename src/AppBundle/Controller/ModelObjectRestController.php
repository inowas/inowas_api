<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ModelObjectRestController extends FOSRestController
{
    /**
     * Return the modelobject list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the modelobject list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserModelobjectsAction($username)
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

        $modelObjects = $this->getDoctrine()
            ->getRepository('AppBundle:ModelObject')
            ->findBy(
                array('owner' => $user),
                array('id' => 'ASC')
            )
        ;

        $view = View::create();
        $view->setData($modelObjects)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('list'))
                ->setSerializeNull(true)
                ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }

    /**
     * Return a ModelObject by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an ModelObject by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id id
     * @return View
     */
    public function getModelobjectAction($id)
    {
        $area = $this->getDoctrine()
            ->getRepository('AppBundle:ModelObject')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$area)
        {
            throw $this->createNotFoundException('ModelObject with id='.$id.' not found.');
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
