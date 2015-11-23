<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class StreamRestController extends FOSRestController
{
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
     * @param string $id stream-id
     *
     * @return View
     */
    public function getStreamAction($id)
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
            $view->setData($stream)->setStatusCode(200);
            return $view;
        } else
        {
            throw $this->createAccessDeniedException();
        }
    }
}
