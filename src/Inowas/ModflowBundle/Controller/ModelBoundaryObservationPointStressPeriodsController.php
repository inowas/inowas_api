<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Inowas\ModflowBundle\Model\Boundary\ObservationPoint;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ModelBoundaryObservationPointStressPeriodsController extends FOSRestController
{
    /**
     * * @Get("/observationpoint/{id}/stressperiods")
     *
     * Returns a list of stressperiods by observationpoint id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of stressperiods by observationpoint id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @param $id
     * @return View
     */
    public function getObservationPointStressperiodsAction($id)
    {
        $manager = $this->get('inowas.modflow.observationpointmanager');
        $observationPoint = $manager->findById($id);

        if (! $observationPoint instanceof ObservationPoint){
            throw $this->createNotFoundException(sprintf('ObservationPoint with id=%s not found.', $id));
        }

        $view = View::create($observationPoint->getStressPeriods())
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * @Put("/observationpoint/{id}/stressperiods")
     *
     * Update stressperiods of an observation point.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update stressperiods of an observation point.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param $id
     * @param ParamFetcher $paramFetcher
     *
     * @RequestParam(name="data", nullable=false, strict=true, description="Stressperiod-Data in Json-Format")
     *
     * @return View
     */
    public function putObservationPointStressPeriodsAction($id, ParamFetcher $paramFetcher)
    {
        $manager = $this->get('inowas.modflow.observationpointmanager');
        $observationPoint = $manager->findById($id);

        if (! $observationPoint instanceof ObservationPoint){
            throw $this->createNotFoundException(sprintf('ObservationPoint with id=%s not found.', $id));
        }

        $manager->updateStressPeriods($observationPoint, $paramFetcher->get('data'));

        $view = View::create($observationPoint->getStressPeriods())
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

}