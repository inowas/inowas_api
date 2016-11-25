<?php

namespace Inowas\ModflowBundle\Controller;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Inowas\ModflowBundle\Model\Boundary\Boundary;
use Inowas\ModflowBundle\Model\Boundary\ObservationPoint;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ObservationPointController extends FOSRestController
{
    /**
     * Add an observation point to the boundary.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Add an observation point to the boundary.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Post("/boundaries/{id}")
     * @param $id
     * @param ParamFetcher $paramFetcher
     *
     * @Rest\RequestParam(name="lng", nullable=false, strict=true, description="Longitude of the observation point")
     * @Rest\RequestParam(name="lat", nullable=false, strict=true, description="Latitude of the observation point")
     * @Rest\RequestParam(name="srid", nullable=false, strict=false, default=4326, description="Name of the new Boundary.")
     *
     * @return View
     */
    public function postBoundaryObservationPointAction($id, ParamFetcher $paramFetcher)
    {
        $boundaryManager = $this->get('inowas.modflow.boundarymanager');
        $boundary = $boundaryManager->findById($id);

        $observationPoint = $this->get('inowas.modflow.observationpointmanager')->create();

        $lat = (float)$paramFetcher->get('lat');
        $lng = (float)$paramFetcher->get('lng');
        $srid = (int)$paramFetcher->get('srid');

        $observationPoint->setGeometry(new Point($lng, $lat, $srid));
        $boundary->addObservationPoint($observationPoint);
        $boundaryManager->update($boundary);

        $view = View::create($boundary)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * Returns the observationpoint details specified by boundaryId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the observationpoint details by id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/observationpoint/{id}")
     * @param $id
     * @return View
     */
    public function getObservationPointAction($id)
    {
        $manager = $this->get('inowas.modflow.observationpointmanager');
        $observationPoint = $manager->findById($id);

        if (! $observationPoint instanceof ObservationPoint){
            throw $this->createNotFoundException(sprintf('ObservationPoint with id=%s not found.', $id));
        }

        $view = View::create($observationPoint)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * Updates the observationPoint details specified by observationPoint-Id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates the observationPoint details specified by observationPoint-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Put("/observationpoint/{id}")
     * @param $id
     * @param ParamFetcher $paramFetcher
     *
     * @Rest\RequestParam(name="lng", nullable=false, strict=true, description="Longitude of the observation point")
     * @Rest\RequestParam(name="lat", nullable=false, strict=true, description="Latitude of the observation point")
     * @Rest\RequestParam(name="srid", nullable=false, strict=false, default=4326, description="Name of the new Boundary.")
     *
     * @return View
     */
    public function putObservationPointAction($id, ParamFetcher $paramFetcher)
    {
        $manager = $this->get('inowas.modflow.observationpointmanager');
        $observationPoint = $manager->findById($id);


        $lat = (float)$paramFetcher->get('lat');
        $lng = (float)$paramFetcher->get('lng');
        $srid = (int)$paramFetcher->get('srid');

        $observationPoint->setGeometry(new Point($lng, $lat, $srid));
        $manager->update($observationPoint);

        $view = View::create($observationPoint)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * Returns a list of observationpoints from a boundary specified by boundary id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of observationpoints from a boundary specified by boundary id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/boundaries/{id}/observationpoints")
     * @param $id
     * @return View
     * @throws NotFoundHttpException
     */
    public function getBoundaryObservationPointsAction($id)
    {

        $manager = $this->get('inowas.modflow.boundarymanager');
        $boundary = $manager->findById($id);

        if (! $boundary instanceof Boundary){
            throw $this->createNotFoundException(sprintf('Boundary with id=%s not found.', $id));
        }

        $view = View::create($boundary->getObservationPoints())
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }
}
