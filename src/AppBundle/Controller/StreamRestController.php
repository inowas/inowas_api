<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class StreamRestController extends FOSRestController
{
    /**
     * Return the stream list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the stream list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserStreamsAction($username)
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

        $streams = $this->getDoctrine()
            ->getRepository('AppBundle:Stream')
            ->findBy(
                array('owner' => $user),
                array('id' => 'ASC')
            )
        ;

        $view = View::create();
        $view->setData($streams)
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
     * Return a stream by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a stream by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id id
     *
     * @return View
     */
    public function getStreamsAction($id)
    {
        $stream = $this->getDoctrine()
            ->getRepository('AppBundle:Stream')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$stream)
        {
            throw $this->createNotFoundException('Stream with id='.$id.' not found.');
        }

        if ($stream->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $stream->getOwner())
        {
            $view = View::create();
            $view->setData($stream)
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