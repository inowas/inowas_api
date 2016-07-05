<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

class SoilModelRestController extends FOSRestController
{
    /**
     * Return the overall soilmodel list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall soilmodel list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserSoilmodelsAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (!$user) {
            throw $this->createNotFoundException('User with username '.$username.' not found.');
        }

        $soilModels = $this->getDoctrine()
            ->getRepository('AppBundle:SoilModel')
            ->findBy(
                array(
                    'owner' => $user,
                    'public' => true
                ),
                array('id' => 'ASC')
            );

        $view = View::create();
        $view->setData($soilModels)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('soilmodellist'))
            )
        ;

        return $view;
    }

    /**
     * Return the soilmodel details by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the soilmodel details by id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getSoilmodelsAction($id)
    {

        try{
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Soilmodel with id='.$id.' not found.');
        }

        $soilModel = $this->getDoctrine()
            ->getRepository('AppBundle:SoilModel')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (!$soilModel) {
            throw $this->createNotFoundException('Soilmodel with id='.$id.' not found.');
        }

        $view = View::create();
        $view->setData($soilModel)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('soilmodeldetails')));

        return $view;
    }

    /**
     * Return all layers from a soilmodel (id) sorted from top to bottom.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return all layers from a soilmodel (id) sorted from top to bottom.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getSoilmodelsGeologicallayersAction($id)
    {

        try{
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Soilmodel with id='.$id.' not found.');
        }

        $soilModel = $this->getDoctrine()
            ->getRepository('AppBundle:SoilModel')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (!$soilModel) {
            throw $this->createNotFoundException('Soilmodel with id='.$id.' not found.');
        }

        $view = View::create();
        $view->setData($soilModel->getSortedGeologicalLayers())
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('soilmodellayers')));

        return $view;
    }
}
