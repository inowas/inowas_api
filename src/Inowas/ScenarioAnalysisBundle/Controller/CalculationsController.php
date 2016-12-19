<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Service\HeadsManager;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;
use Inowas\ScenarioAnalysisBundle\Service\ScenarioManager;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalculationsController extends FOSRestController
{

    /**
     * Sends the command to calculate the scenario by scenario-id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Sends the command to calculate the scenario by scenario-id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     *
     * @Rest\Post("/calculation/{scenarioId}")
     * @param $scenarioId
     * @return View
     * @throws
     */
    public function postScenarioAnalysisCalculationAction($scenarioId)
    {

        /** @var Scenario $scenario */
        $scenario = $this->get('inowas.scenarioanalysis.scenariomanager')->findById($scenarioId);

        if (! $scenario instanceof Scenario) {
            throw new InvalidArgumentException(sprintf('There is no scenario with id = %s', $scenarioId));
        }

        $flopy = $this->get('inowas.flopy');
        $calculation = $flopy->addScenarioToQueue($scenario);
        $flopy->startAsyncFlopyProcessRunner();

        $view = View::create($calculation)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * Return the list of available ModflowPackages from a Calculation Id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the list of available ModflowPackages from a ModelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/calculation/{id}/packages")
     * @param $id
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function getScenarioCalculationPropertiesAction($id)
    {
        $calculation = $this->get('inowas.modflow.calculationmanager')->findById($id);
        return new JsonResponse($calculation->getCalculationProperties(), 200);
    }

    /**
     * Returns the flopy package data as json.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the flopy package data as json.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/calculation/{scenarioId}/packages/{packageName}")
     * @param $scenarioId
     * @param $packageName
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function getScenarioModelPackageAction($scenarioId, $packageName)
    {
        /** @var Scenario $scenario */
        $scenario = $this->get('inowas.scenarioanalysis.scenariomanager')->findById($scenarioId);

        if (! $scenario instanceof Scenario) {
            throw new InvalidArgumentException(sprintf('There is no scenario with id = %s', $scenarioId));
        }

        /** @var ModflowModel $model */
        $basemodel = $this->get('inowas.modflow.toolmanager')->findModelById($scenario->getBaseModelId());

        if (! $basemodel instanceof ModflowModel) {
            throw new InvalidArgumentException(sprintf('There is no modflow-baseModel with id = %s', $scenario->getBaseModelId()));
        }

        $model = $scenario->applyTo($basemodel);
        $soilmodel = $this->get('inowas.soilmodel.soilmodelmanager')->findById($model->getSoilmodelId());

        $packageManager = $this->get('inowas.flopy.packagemanager');
        return new JsonResponse($packageManager->getPackageData($model, $soilmodel, $packageName), 200);
    }

    /**
     * Posts the headData
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
     * @Rest\Post("/calculation/{id}/heads")
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @Rest\RequestParam(name="heads", nullable=false, strict=false, description="The heads array")
     * @Rest\RequestParam(name="totim", nullable=false, strict=false, description="The totim")
     *
     * @return View
     */
    public function postScenarioHeadsAction(ParamFetcher $paramFetcher, $id)
    {

        $heads = json_decode($paramFetcher->get('heads'));
        $totim = $paramFetcher->get('totim');

        /** @var ScenarioManager $scenarioManager */
        $scenarioManager = $this->get('inowas.scenarioanalysis.scenariomanager');
        $scenario = $scenarioManager->findById($id);

        /** @var HeadsManager $headsManager */
        $headsManager = $this->get('inowas.modflow.headsmanager');

        foreach ( $heads as $layerNumber => $data){
            $headsManager->addHead($scenario, $totim, $layerNumber, $data);
        }

        $view = View::create('OK')->setStatusCode(200);
        return $view;
    }
}
