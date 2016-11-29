<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Inowas\ModflowBundle\Model\Boundary\ObservationPoint;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class StressPeriodController extends FOSRestController
{
    /**
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
     * @Rest\Get("/observationpoints/{id}/stressperiods")
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

        /** @var array $stressPeriods */
        $stressPeriods = $observationPoint->getStressPeriods()->toArray();

        usort($stressPeriods, function($a, $b) {
           return $a->getDateTimeBegin() <=> $b->getDateTimeBegin();
        });

        $view = View::create($stressPeriods)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
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
     * @Rest\Put("/observationpoints/{id}/stressperiods")
     * @Rest\RequestParam(name="data", nullable=false, strict=true, description="Stressperiod-Data in Json-Format")
     *
     * @param $id
     * @param ParamFetcher $paramFetcher
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
