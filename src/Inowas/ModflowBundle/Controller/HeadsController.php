<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;

class HeadsController extends FOSRestController
{

    /**
     * Returns a list of heads over time by modelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of heads over time by modelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/models/{id}/heads")
     * @param $id
     * @return View
     */
    public function getModflowModelHeadListAction($id)
    {

        $model = $this->get('inowas.modflow.toolmanager')->findModelById($id);
        $heads = $this->get('inowas.modflow.headsmanager')->getList($model);

        $view = View::create($heads)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('list'))
            );

        return $view;
    }
}
