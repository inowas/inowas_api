<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class GeologicalLayerRestController extends FOSRestController
{
    /**
     * Return a list of geological layers by username
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a list of geological layers by username",
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
    public function getUserGeologicallayersAction($username)
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

        $geologicalLayers = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findBy(
                array('owner' => $user),
                array('id' => 'ASC')
            )
        ;

        $view = View::create();
        $view->setData($geologicalLayers)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups('modelobjectlist')
            )
        ;

        return $view;
    }

    /**
     * Return a geological layer by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a geological layer by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id GeologicalLayer-Id
     *
     * @return View
     */
    public function getGeologicallayersAction($id)
    {
        /** @var GeologicalLayer $geologicalLayers */
        $geologicalLayer = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($geologicalLayer->getPublic() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $geologicalLayer->getOwner())
        {
            $view = View::create();
            $view->setData($geologicalLayer)
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
