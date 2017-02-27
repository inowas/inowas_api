<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Inowas\Modflow\Model\CalculationResultType;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Inowas\Modflow\Model\UserId;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidUuidException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;

class ScenarioAnalysisController extends FOSRestController
{
    /**
     * Get ScenarioAnalysis detail by BaseModelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get ScenarioAnalysis details from current user and basemodelId.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/{baseModelId}")
     * @param $baseModelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenariosAnalysisModelScenariosAction($baseModelId)
    {
        if (! Uuid::isValid($baseModelId)){
            throw new InvalidUuidException();
        }

        return new JsonResponse($this->get('inowas.model_scenarios_finder')
            ->findByUserAndBaseModelId(
                UserId::fromString($this->getUser()->getId()->toString()),
                ModflowId::fromString($baseModelId)
            ));
    }

    /**
     * Get Modeldetails by baseModelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get Modeldetails by baseModelIs.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/model/{baseModelId}")
     * @param $baseModelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisBaseModelDetailsAction($baseModelId)
    {
        if (! Uuid::isValid($baseModelId)){
            throw new InvalidUuidException();
        }

        return new JsonResponse($this->get('inowas.modflow_projection.model_details_finder')
            ->findByBaseModelId(
                ModflowId::fromString($baseModelId)
            )
        );
    }

    /**
     * Get Boundaries by modelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get Boundaries by modelId.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/model/{modelId}/boundaries")
     * @param $modelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisModelBoundariesAction($modelId)
    {
        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        return new JsonResponse($this->get('inowas.model_boundaries_finder')
            ->findByModelId(
                ModflowId::fromString($modelId)
            )
        );
    }

    /**
     * Get totalTimes of last calculation of model by modelId, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get Times by CalculationId, Type and Layer.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/model/{modelId}/calculation/times/type/{type}/layer/{layer}")
     * @param $modelId
     * @param $type
     * @param $layer
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisModelCalculationTimesByTypeAndLayerAction($modelId, $type, $layer)
    {
        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        $calculation = $this->get('inowas.modflow_projection.calculation_list_finder')
            ->findLastCalculationByModelId(ModflowId::fromString($modelId));

        $totalTimes = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findTimes(
                ModflowId::fromString($calculation['calculation_id']),
                CalculationResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer)
        );

        $result = [
            'start_date' => $calculation['date_time_start'],
            'end_date' => $calculation['date_time_end'],
            'total_times' => $totalTimes
        ];

        return new JsonResponse($result);
    }

    /**
     * Get totalTimes of calculation calculationId, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get totalTimes of calculation calculationId, Type and Layer.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/model/calculation/{calculationId}/times/type/{type}/layer/{layer}")
     * @param $calculationId
     * @param $type
     * @param $layer
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisCalculationTimesByTypeAndLayerAction($calculationId, $type, $layer)
    {
        if (! Uuid::isValid($calculationId)){
            throw new InvalidUuidException();
        }

        $calculation = $this->get('inowas.modflow_projection.calculation_list_finder')
            ->findCalculationById(ModflowId::fromString($calculationId));

        $totalTimes = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findTimes(
                ModflowId::fromString($calculation['calculation_id']),
                CalculationResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer)
            );

        $result = [
            'start_date' => $calculation['date_time_start'],
            'end_date' => $calculation['date_time_end'],
            'total_times' => $totalTimes
        ];

        return new JsonResponse($result);
    }

    /**
 * Get models last calculation result by modelId, type and layerNumber.
 *
 * @ApiDoc(
 *   resource = true,
 *   description = "Get models last calculation result by modelId, type and layerNumber.",
 *   statusCodes = {
 *     200 = "Returned when successful"
 *   }
 * )
 *
 * @Rest\Get("/model/{modelId}/calculation/result/type/{type}/layer/{layer}/totim/{totim}")
 * @param $modelId
 * @param $type
 * @param $layer
 * @param $totim
 * @return JsonResponse
 * @throws InvalidUuidException
 * @throws InvalidArgumentException
 */
    public function getScenarioAnalysisResultByModelResultTypeAndLayerAction($modelId, $type, $layer, $totim)
    {
        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        $calculation = $this->get('inowas.modflow_projection.calculation_list_finder')
            ->findLastCalculationByModelId(ModflowId::fromString($modelId));

        $result = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculation['calculation_id']),
                CalculationResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );

        return new JsonResponse($result);
    }

    /**
     * Get calculation result by calculationId, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get models last calculation result by calculationId, type and layerNumber.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/model/calculation/{calculationId}/result/type/{type}/layer/{layer}/totim/{totim}")
     * @param $calculationId
     * @param $type
     * @param $layer
     * @param $totim
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisResultByCalculationResultTypeAndLayerAction($calculationId, $type, $layer, $totim)
    {
        if (! Uuid::isValid($calculationId)){
            throw new InvalidUuidException();
        }

        $result = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculationId),
                CalculationResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );

        return new JsonResponse($result);
    }

    /**
     * Get Information about what results are available in which layer.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get Information about what results (heads, drawdowns, watertables) are available in which layer, by modelId",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/model/{modelId}/calculation/layervalues")
     * @param $modelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisModelCalculationLayerValuesAction($modelId)
    {
        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        $calculation = $this->get('inowas.modflow_projection.calculation_list_finder')
            ->findLastCalculationByModelId(ModflowId::fromString($modelId));

        $layerValues = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findLayerValues(ModflowId::fromString($calculation['calculation_id']));

        return new JsonResponse($layerValues);
    }

    /**
     * Get Information about what results are available in which layer.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get Information about what results (heads, drawdowns, watertables) are available in which layer, by calculationId",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/calculation/{calculationId}/layervalues")
     * @param $calculationId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisCalculationLayerValuesAction($calculationId)
    {
        if (! Uuid::isValid($calculationId)){
            throw new InvalidUuidException();
        }

        $layerValues = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findLayerValues(ModflowId::fromString($calculationId));

        return new JsonResponse($layerValues);
    }


}
