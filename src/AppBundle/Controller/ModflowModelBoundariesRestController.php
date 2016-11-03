<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BoundaryModelObject;
use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\ModflowModelScenario;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Entity\User;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\EventFactory;
use AppBundle\Model\LatLng;
use AppBundle\Model\LineStringFactory;
use AppBundle\Model\PointFactory;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModflowModelBoundariesRestController extends FOSRestController
{
    /**
     * Returns the area by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the area by ModflowModel-Id.",
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
    public function getModflowmodelAreaAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $model = $this->setMutable($model, $this->getUser());

        $area = $model->getArea();

        if (is_null($area)){
            throw new NotFoundHttpException('Area not found');
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($area)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Boundaries by ModflowModel-Id.",
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
    public function getModflowmodelBoundariesAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $model = $this->setMutable($model, $this->getUser());

        $boundaries = $model->getBoundaries();

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($boundaries)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Constant Head Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Constant Head Boundaries by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return Response|View
     */
    public function getModflowmodelConstant_headAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $model = $this->setMutable($model, $this->getUser());

        $constantHeadBoundaries = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof ConstantHeadBoundary) {
                $constantHeadBoundaries[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext
            ->setGroups("boundarylist")
        ;

        $view = View::create();
        $view->setData($constantHeadBoundaries)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all General Head Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all General Head Boundaries by ModflowModel-Id.",
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
    public function getModflowmodelGeneral_headAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $model = $this->setMutable($model, $this->getUser());

        $ghb = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof GeneralHeadBoundary) {
                $ghb[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($ghb)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Wells by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Wells by ModflowModel-Id.",
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
    public function getModflowmodelWellsAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $model = $this->setMutable($model, $this->getUser());

        $wells = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof WellBoundary) {
                $wells[] = $boundary;
            }
        }

        $response = array();
        /** @var WellBoundary $well */
        foreach ($wells as $well) {
            $response[$well->getWellType()][] = $well;
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($response)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Rivers by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Rivers by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $modelId
     * @param $wellId
     * @param Request $request
     *
     * @return Response|View
     */
    public function putModflowmodelWellsAction($modelId, $wellId, Request $request)
    {
        if ($this->isScenario($modelId)) {
            $scenario = $this->getDoctrine()->getRepository('AppBundle:ModflowModelScenario')
                ->findOneBy(array(
                    'id' => $modelId
                ));

            $well = $this->getDoctrine()->getRepository('AppBundle:WellBoundary')
                ->findOneBy(array(
                    'id' => $wellId
                ));


            if ($request->request->has('latLng')) {

                $newWell = clone $well;

                $latLng = LatLng::fromJson($request->request->get('latLng'));

                $newWell->setGeometry(PointFactory::fromLatLng($latLng));
                $this->getDoctrine()->getManager()->persist($newWell);
                $this->getDoctrine()->getManager()->flush();

                $newWell->setActiveCells(
                    $this->get('inowas.geotools')
                        ->getActiveCells(
                            $newWell,
                            $scenario->getBaseModel()->getBoundingBox(),
                            $scenario->getBaseModel()->getGridSize()
                        )
                );

                $scenario->addEvent(EventFactory::createChangeBoundaryEvent($well, $newWell));
                $this->getDoctrine()->getManager()->merge($scenario);
                $this->getDoctrine()->getManager()->flush();
            }

            $view = View::create();
            $view->setData('OK');

            return $view;
        }


        /** @var ModFlowModel $model */
        $model = $this->findModelById($modelId);
        $model = $this->setMutable($model, $this->getUser());

        if ($model->getOwner() != $this->getUser()){
            throw new AccessDeniedException(
                sprintf('User %s has no access Model id: %s from User %s',
                    $this->getUser()->getUserName(),
                    $model->getId()->toString(),
                    $model->getOwner()->getUsername()
                )
            );
        }

        $wells = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof WellBoundary) {
                $wells[] = $boundary;
            }
        }

        /** @var WellBoundary $well */
        foreach ($wells as $well){
            if ($well->getId()->toString() == $wellId){
                break;
            }
        }

        if ($request->request->has('latLng')){
            $latLng = LatLng::fromJson($request->request->get('latLng'));
            $well->setGeometry(PointFactory::fromLatLng($latLng));
            $this->getDoctrine()->getManager()->persist($well);
            $this->getDoctrine()->getManager()->flush();

            $activeCells = $this->get('inowas.geotools')->getActiveCells($well, $model->getBoundingBox(), $model->getGridSize());
            $well->setActiveCells($activeCells);
            $this->getDoctrine()->getManager()->persist($well);
            $this->getDoctrine()->getManager()->flush();
        }

        $response = array();
        /** @var WellBoundary $well */
        foreach ($wells as $well) {
            $response[$well->getWellType()][] = $well;
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($response)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Rivers by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Rivers by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return Response|View
     */
    public function getModflowmodelRiversAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $model = $this->setMutable($model, $this->getUser());

        $rivers = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof StreamBoundary) {
                $rivers[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($rivers)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Rivers by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Rivers by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $modelId
     * @param $riverId
     * @param Request $request
     *
     * @return Response|View
     */
    public function putModflowmodelRiversAction($modelId, $riverId, Request $request)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($modelId);
        $model = $this->setMutable($model, $this->getUser());

        if ($model->getOwner() != $this->getUser()){
            throw new AccessDeniedException(
                sprintf('User %s has no access Model id: %s from User %s',
                    $this->getUser()->getUserName(),
                    $model->getId()->toString(),
                    $model->getOwner()->getUsername()
                )
            );
        }

        $rivers = [];
        foreach ($model->getBoundaries() as $boundary){
            if ($boundary instanceof StreamBoundary){
                $rivers[] = $boundary;
            }
        }

        $river = null;
        /** @var BoundaryModelObject $boundary */
        foreach ($rivers as $river){
            if ($river->getId()->toString() == $riverId){
                break;
            }
        }

        if ($request->request->has('latLngs')){
            $river->setGeometry(LineStringFactory::fromLatLngs(json_decode($request->request->get('latLngs'))));
            $this->getDoctrine()->getManager()->persist($river);
            $this->getDoctrine()->getManager()->flush();

            $activeCells = $this->get('inowas.geotools')->getActiveCells($river, $model->getBoundingBox(), $model->getGridSize());
            $river->setActiveCells($activeCells);
            $this->getDoctrine()->getManager()->persist($river);
            $this->getDoctrine()->getManager()->flush();
        }

        if ($request->request->has('activeCells')){
            $river->setActiveCells(ActiveCells::fromObject(json_decode($request->request->get('activeCells'))));
            $this->getDoctrine()->getManager()->persist($river);
            $this->getDoctrine()->getManager()->flush();
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($rivers)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * @param $id
     * @return \AppBundle\Entity\AbstractModel
     */
    private function findModelById($id)
    {

        if (!Uuid::isValid($id)){
            throw $this->createNotFoundException('Model with id='.$id.' not found.');
        }

        $scenario = $this->getDoctrine()
            ->getRepository('AppBundle:ModflowModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($scenario instanceof ModflowModelScenario) {
            return $scenario->getModel();
        }

        $model = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        return $model;
    }

    /**
     * @param ModFlowModel $model
     * @param User $user
     * @return ModFlowModel
     */
    private function setMutable(ModFlowModel $model, User $user){

        if ($model->getOwner() == $user){
            $model->getArea()->setMutable(true);

            /** @var BoundaryModelObject $boundary */
            foreach ($model->getBoundaries() as $boundary) {
                $boundary->setMutable(true);
            }

            /** @var BoundaryModelObject $boundary */
            foreach ($model->getObservationPoints() as $observationPoint) {
                $observationPoint->setMutable(true);
            }
        }

        return $model;
    }

    private function isScenario($id){
        return $this->getDoctrine()->getRepository('AppBundle:ModflowModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));
    }
}
