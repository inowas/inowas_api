<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Model\Events\AddWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellLayerNumberEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellNameEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellStressperiodsEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellTypeEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\MoveWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\RemoveWellEvent;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Ramsey\Uuid\Uuid;

class ScenarioController extends FOSRestController
{
    /**
     * Load Scenarios from a Model.
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
     * @Rest\Get("/model/{modelId}/scenarios")
     * @return View
     */
    public function getScenariosAction($modelId)
    {
        $scenarioManager = $this->get('inowas.scenarioanalysis.scenariomanager');

        $view = View::create($scenarioManager->findByModelId($modelId))
            ->setStatusCode(200)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(array('list'))
                    ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }

    /**
     * Create a new Scenario.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a new Scenario.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Post("/model/{modelId}/scenarios")
     * @param string $modelId
     * @param ParamFetcher $paramFetcher
     * @Rest\RequestParam(name="name", nullable=false, strict=true, description="Name of the scenario.")
     * @Rest\RequestParam(name="description", strict=false, description="Description of scenario.", default="")
     *
     * @return View
     */
    public function postScenarioAction(ParamFetcher $paramFetcher, $modelId)
    {
        $modelManager = $this->get('inowas.modflow.modelmanager');
        $model = $modelManager->findById($modelId);

        $scenarioManager = $this->get('inowas.scenarioanalysis.scenariomanager');
        $scenario = $scenarioManager->create($model);
        $scenario->setName($paramFetcher->get('name'));
        $scenario->setDescription($paramFetcher->get('description'));
        $scenarioManager->update($scenario);

        $view = View::create($scenario)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
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
     * @Rest\Put("/model/{modelId}/scenarios/{scenarioId}")
     * @param string $modelId
     * @param string $scenarioId
     * @param ParamFetcher $paramFetcher
     * @Rest\RequestParam(name="name", nullable=false, strict=true, description="Name of the scenario.")
     * @Rest\RequestParam(name="description", strict=false, description="Description of scenario.")
     *
     * @return View
     * @throws InvalidArgumentException
     */
    public function putScenarioAction(ParamFetcher $paramFetcher, $modelId, $scenarioId)
    {
        $scenarioManager = $this->get('inowas.scenarioanalysis.scenariomanager');
        $scenario = $scenarioManager->findById($scenarioId);

        if (! $modelId == $scenario->getBaseModel()->getId()->toString()){
            throw new InvalidArgumentException(sprintf('Scenario with id %s has no Basemodel with Id %s', $scenario->getId()->toString(), $modelId));
        }

        $scenario->setName($paramFetcher->get('name'));
        $scenario->setDescription($paramFetcher->get('description'));
        $scenarioManager->update($scenario);

        $view = View::create($scenario)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

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
     * @Rest\Post("/model/{modelId}/scenarios/{scenarioId}")
     * @param string $modelId
     * @param string $scenarioId
     * @param ParamFetcher $paramFetcher
     * @Rest\RequestParam(name="type", nullable=false, strict=true, description="Type of event.")
     * @Rest\RequestParam(name="payload", strict=false, description="Payload.")
     *
     * @return View
     * @throws InvalidArgumentException
     */
    public function postEventsToScenario(ParamFetcher $paramFetcher, $modelId, $scenarioId)
    {
        $scenarioManager = $this->get('inowas.scenarioanalysis.scenariomanager');
        $scenario = $scenarioManager->findById($scenarioId);

        if (! $modelId == $scenario->getBaseModel()->getId()->toString()){
            throw new InvalidArgumentException(sprintf('Scenario with id %s has no Basemodel with Id %s', $scenario->getId()->toString(), $modelId));
        }

        $payload = json_decode($paramFetcher->get('payload'));

        switch ($payload->event) {
            case 'ADD_WELL':
                $name = $payload->name;
                $scenario->addEvent(new AddWellEvent($name));
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
                $newLocation = new Point($payload->geometry->lat, $payload->geometry->lng, $payload->geometry->srid);
                $scenario->addEvent(new MoveWellEvent($wellId, $newLocation));
                break;
            case 'REMOVE_WELL':
                $wellId = Uuid::fromString($payload->well_id);
                $scenario->addEvent(new RemoveWellEvent($wellId));
                break;
        }

        $view = View::create($scenario)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }
}
