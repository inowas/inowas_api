<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

class ModflowModelScenariosRestController extends FOSRestController
{
    /**
     * Returns a list of scenarios by ModflowModel-Id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of scenarios by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getModflowmodelScenariosAction($id)
    {

        if (!Uuid::isValid($id)){
            throw $this->createNotFoundException('Model with id='.$id.' not found.');
        }

        $scenarios = $this->getDoctrine()->getRepository('AppBundle:ModflowModelScenario')
            ->findBy(
                array(
                    'baseModel' => $id,
                    'owner' => $this->getUser()
                ),
                array('order' => 'ASC')
            );

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('scenariodetails');

        $view = View::create();
        $view->setData($scenarios)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }
}
