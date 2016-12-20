<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;

class ScenarioHeadsController extends FOSRestController
{

    /**
     * Returns a list of heads over time by scenarioId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of heads over time by scenarioId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/scenario/{scenarioId}/heads")
     * @param $scenarioId
     * @return View
     */
    public function getModflowModelHeadListAction($scenarioId)
    {

        $scenario = $this->get('inowas.scenarioanalysis.scenariomanager')->findById($scenarioId);
        $heads = $this->get('inowas.modflow.headsmanager')->getList($scenario);

        $view = View::create($heads)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('list'))
            );

        return $view;
    }
}
