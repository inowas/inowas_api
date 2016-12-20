<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserInterface;
use Inowas\ModflowBundle\Model\Boundary\Boundary;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidUuidException;
use Inowas\ScenarioAnalysisBundle\Model\Events\AddWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellLayerNumberEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellNameEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellStressperiodsEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellTypeEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\MoveWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\RemoveWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;
use Inowas\ScenarioAnalysisBundle\Model\ScenarioAnalysis;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Ramsey\Uuid\Uuid;

class ScenarioController extends FOSRestController
{
    /**
     * Load Scenarios from a Model and User.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Load Scenarios from a Model.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $modelId
     * @Rest\Get("/models/{modelId}/scenarios")
     * @return View
     * @throws InvalidArgumentException
     * @throws InvalidUuidException
     */
    public function getScenariosAction($modelId)
    {
        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        /** @var UserInterface $user */
        $user = $this->getUser();

        $scenarioAnalysisManager = $this->get('inowas.scenarioanalysis.scenarioanalysismanager');
        $scenarioAnalysis = $scenarioAnalysisManager->findByUserIdAndBasemodelId($user->getId(), Uuid::fromString($modelId));

        if (! $scenarioAnalysis instanceof ScenarioAnalysis){
            throw new InvalidArgumentException('ScenarioAnalysis not available.');
        }

        $scenarios = $scenarioAnalysis->getScenarios();
        $view = View::create($scenarios)->setStatusCode(200);
        return $view;
    }

    /**
     * Returns the scenario model details specified by scenarioId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the model details specified by modelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/models/{modelId}/scenarios/{scenarioId}")
     * @param $modelId
     * @param $scenarioId
     * @return View
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAction($modelId, $scenarioId)
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        $modelManager = $this->get('inowas.modflow.toolmanager');
        $baseModel = $modelManager->findModelById($modelId);

        if (! $baseModel instanceof ModflowModel){
            throw new InvalidArgumentException();
        }

        $scenarioAnalysisManager = $this->get('inowas.scenarioanalysis.scenarioanalysismanager');
        $scenarioAnalysis = $scenarioAnalysisManager->findByUserIdAndBasemodelId($user->getId(), Uuid::fromString($modelId));

        $myScenario = null;
        foreach ($scenarioAnalysis->getScenarios() as $scenario){
            if ($scenario->getId()->toString() == $scenarioId){
                $myScenario = $scenario;
                break;
            }
        }

        if (! $myScenario instanceof Scenario){
            throw new InvalidArgumentException();
        }

        $model = $myScenario->applyTo($baseModel);

        $view = View::create($model)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
                ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }

    /**
     * Update scenario.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update scenario.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Put("/models/{modelId}/scenarios/{scenarioId}")
     * @param string $modelId
     * @param string $scenarioId
     * @param ParamFetcher $paramFetcher
     * @Rest\RequestParam(name="name", nullable=false, strict=true, description="Name of the scenario.")
     * @Rest\RequestParam(name="description", strict=false, description="Description of scenario.")
     *
     * @return View
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function putScenarioAction(ParamFetcher $paramFetcher, $modelId, $scenarioId)
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        $modelManager = $this->get('inowas.modflow.toolmanager');
        $baseModel = $modelManager->findModelById($modelId);

        if (! $baseModel instanceof ModflowModel){
            throw new InvalidArgumentException();
        }

        $scenarioAnalysisManager = $this->get('inowas.scenarioanalysis.scenarioanalysismanager');
        $scenarioAnalysis = $scenarioAnalysisManager->findByUserIdAndBasemodelId($user->getId(), Uuid::fromString($modelId));

        $myScenario = null;
        foreach ($scenarioAnalysis->getScenarios() as $scenario){
            if ($scenario->getId()->toString() == $scenarioId){
                $myScenario = $scenario;
                break;
            }
        }

        if (! $myScenario instanceof Scenario){
            throw new InvalidArgumentException();
        }

        $myScenario->setName($paramFetcher->get('name'));
        $myScenario->setDescription($paramFetcher->get('description'));
        $scenarioAnalysisManager->update($scenarioAnalysis);

        $view = View::create($scenarioAnalysis->getScenarios()->toArray())->setStatusCode(200);
        return $view;
    }

    /**
     * Add event to scenario.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Add event to scenario.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Post("/models/{modelId}/scenarios/{scenarioId}")
     * @param string $modelId
     * @param string $scenarioId
     * @param ParamFetcher $paramFetcher
     * @Rest\RequestParam(name="type", nullable=false, strict=true, description="Type of event.")
     * @Rest\RequestParam(name="payload", strict=false, description="Payload.")
     *
     * @return View
     * @throws InvalidArgumentException
     */
    public function postEventsToScenarioAction(ParamFetcher $paramFetcher, $modelId, $scenarioId)
    {
        $scenarioManager = $this->get('inowas.scenarioanalysis.scenariomanager');
        $scenario = $scenarioManager->findById($scenarioId);

        if (! $modelId == $scenario->getBaseModelId()->toString()){
            throw new InvalidArgumentException(sprintf('Scenario with id %s has no Basemodel with Id %s', $scenario->getId()->toString(), $modelId));
        }

        $payload = json_decode($paramFetcher->get('payload'));

        switch ($payload->event) {
            case 'ADD_WELL':
                $name = $payload->name;
                $point = new Point(1,2,3);
                $point->setLatitude($payload->lat);
                $point->setLongitude($payload->lng);
                $point->setSrid($payload->srid);
                $scenario->addEvent(new AddWellEvent($name, $point));
                break;
            case 'CHANGE_WELL_LAYER_NUMBER':
                $wellId = Uuid::fromString($payload->well_id);
                $layerNumber = $payload->layer_number;
                $scenario->addEvent(new ChangeWellLayerNumberEvent($wellId, $layerNumber));
                break;
            case 'CHANGE_WELL_NAME':
                $wellId = Uuid::fromString($payload->well_id);
                $name = $payload->name;
                $scenario->addEvent(new ChangeWellNameEvent($wellId, $name));
                break;
            case 'CHANGE_WELL_STRESSPERIODS':
                $wellId = Uuid::fromString($payload->well_id);
                $stressperiods = [];
                foreach ($payload->stress_periods as $stressperiod){
                    $stressperiods[] = StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime($stressperiod->date_time_begin))
                        ->setFlux($stressperiod->flux);
                }
                $scenario->addEvent(new ChangeWellStressperiodsEvent($wellId, $stressperiods));
                break;
            case 'CHANGE_WELL_TYPE':
                $wellId = Uuid::fromString($payload->well_id);
                $type = $payload->type;
                $scenario->addEvent(new ChangeWellTypeEvent($wellId, $type));
                break;
            case 'MOVE_WELL':
                $wellId = Uuid::fromString($payload->well_id);
                $lat = $payload->geometry->lat;
                $lng = $payload->geometry->lng;
                $srid = $payload->geometry->srid;
                $newLocation = new Point($lat, $lng, $srid);
                $scenario->addEvent(new MoveWellEvent($wellId, $newLocation));
                break;
            case 'REMOVE_WELL':
                $wellId = Uuid::fromString($payload->well_id);
                $scenario->addEvent(new RemoveWellEvent($wellId));
                break;
        }

        $scenarioManager->update($scenario);
        $baseModelId = $scenario->getBaseModelId();

        $baseModel = $this->getDoctrine()->getRepository('InowasModflowBundle:ModflowModel')
            ->findOneBy(array(
                'id' => $baseModelId
            ));

        $baseModel = $scenario->applyTo($baseModel);
        $view = View::create($baseModel)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
                ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }

    /**
     * Returns the boundary details specified by boundary-ID.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the boundary details by id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/models/{modelId}/scenarios/{scenarioId}/boundaries/{boundaryId}")
     * @param $modelId
     * @param $scenarioId
     * @param $boundaryId
     * @return View
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioBoundariesAction($modelId, $scenarioId, $boundaryId)
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        $modelManager = $this->get('inowas.modflow.toolmanager');
        $baseModel = $modelManager->findModelById($modelId);

        if (! $baseModel instanceof ModflowModel){
            throw new InvalidArgumentException();
        }

        $scenarioAnalysisManager = $this->get('inowas.scenarioanalysis.scenarioanalysismanager');
        $scenarioAnalysis = $scenarioAnalysisManager->findByUserIdAndBasemodelId($user->getId(), Uuid::fromString($modelId));

        $myScenario = null;
        foreach ($scenarioAnalysis->getScenarios() as $scenario){
            if ($scenario->getId()->toString() == $scenarioId){
                $myScenario = $scenario;
                break;
            }
        }

        if (! $myScenario instanceof Scenario){
            throw new InvalidArgumentException();
        }

        $model = $myScenario->applyTo($baseModel);

        $myBoundary = null;
        /** @var Boundary $boundary */
        foreach ($model->getBoundaries() as $boundary){
            if ($boundary->getId()->toString() == $boundaryId){
                $myBoundary = $boundary;
            }
        }

        if (! $myBoundary instanceof Boundary){
            throw new InvalidArgumentException();
        }

        $view = View::create($myBoundary)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
                ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }
}
