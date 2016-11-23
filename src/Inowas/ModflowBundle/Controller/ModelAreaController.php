<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ModelAreaController extends FOSRestController
{

    /**
     * @Get("/model/{id}/area")
     *
     * Returns the model area details specified by modelId
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the model area details specified by modelId",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @param $id
     * @return View
     */
    public function getModflowModelAreaAction($id)
    {
        $area = $this->get('inowas.modflow.modelmanager')->findAreaByModelId($id);

        $view = View::create($area)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * @Put("/model/{id}/area")
     *
     * Update the model area specified by modelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update the model area specified by modelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @RequestParam(name="name", nullable=false, strict=false, description="The name of the area")
     * @RequestParam(name="activeCells", nullable=false, strict=false, description="The area active cells")
     * @RequestParam(name="geometry", nullable=false, strict=false, description="The area geometry")
     *
     * @return View
     */
    public function putModflowModelAreaAction(ParamFetcher $paramFetcher, $id)
    {
        $area = $this->get('inowas.modflow.modelmanager')->findAreaByModelId($id);

        if ($paramFetcher->get('name')){
            $area->setName($paramFetcher->get('name'));
        }

        if ($paramFetcher->get('geometry')){
            $area->setGeometry($paramFetcher->get('geometry'));
        }

        $this->get('inowas.modflow.modelmanager')->updateArea($area);

        $view = View::create($area)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }
}
