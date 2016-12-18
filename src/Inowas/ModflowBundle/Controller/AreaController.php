<?php

namespace Inowas\ModflowBundle\Controller;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AreaController extends FOSRestController
{

    /**
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
     * @Rest\Get("/models/{id}/area")
     * @param $id
     * @return View
     */
    public function getModflowModelAreaAction($id)
    {
        $area = $this->get('inowas.modflow.toolmanager')->findAreaByModelId($id);

        $view = View::create($area)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
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
     * @Rest\Put("/models/{id}/area")
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @RequestParam(name="name", nullable=false, strict=false, description="The name of the area")
     * @RequestParam(name="active_cells", nullable=false, strict=false, description="The area active cells")
     * @RequestParam(name="geometry", nullable=false, strict=false, description="The area geometry in geoJson")
     *
     * @return View
     */
    public function putModflowModelAreaAction(ParamFetcher $paramFetcher, $id)
    {
        $area = $this->get('inowas.modflow.toolmanager')->findAreaByModelId($id);

        if ($paramFetcher->get('name')){
            $area->setName($paramFetcher->get('name'));
        }

        if ($paramFetcher->get('geometry')){
            $geometry = \geoPHP::load($paramFetcher->get('geometry'), 'json');
            $polygon = new Polygon($geometry->asArray());
            $area->setGeometry($polygon->setSrid(4326));
        }

        $this->get('inowas.modflow.toolmanager')->updateModelArea($area);

        $view = View::create($area)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }
}
