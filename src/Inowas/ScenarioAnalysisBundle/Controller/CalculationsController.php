<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalculationsController extends FOSRestController
{
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
}
