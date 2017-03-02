<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use HeatMap\HeatMap;
use Inowas\AppBundle\Model\User;
use Inowas\Modflow\Model\HeadData;
use Inowas\Modflow\Model\ResultType;
use Inowas\Modflow\Model\CalculatedResult;
use Inowas\Modflow\Model\ColumnNumber;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\RowNumber;
use Inowas\Modflow\Model\TotalTime;
use Inowas\Modflow\Model\UserId;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidUuidException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ScenarioAnalysisController extends FOSRestController
{

    /**
     * Get list of my scenarioAnalysis-projects.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of my scenarioAnalysis-projects.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/my/projects")
     * @return JsonResponse
     */
    public function getScenarioAnalysisMyProjectsAction(): JsonResponse
    {

        $user = $this->getUser();

        if ($user instanceof User && $user->getId()) {
            $userId = UserId::fromString($this->getUser()->getId()->toString());
            return new JsonResponse(
                $this->get('inowas.modflow_projection.model_details_finder')
                    ->findByBaseUserId($userId)
            );
        }

        return new JsonResponse([]);
    }

    /**
     * Get list of scenarioAnalysis-project by UserId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of scenarioAnalysis-project by UserId.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/user/{userId}")
     * @param $userId
     * @return JsonResponse
     */
    public function getScenarioAnalysisModelsByUserAction($userId): JsonResponse
    {
        $userId = UserId::fromString($userId);

        return new JsonResponse(
            $this->get('inowas.modflow_projection.model_details_finder')
                ->findByBaseUserId($userId)
        );
    }

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
    public function getScenariosAnalysisModelScenariosAction($baseModelId): JsonResponse
    {
        if (! Uuid::isValid($baseModelId)){
            throw new InvalidUuidException();
        }

        return new JsonResponse(
            $this->get('inowas.model_scenarios_finder')->findByUserAndBaseModelId(
                UserId::fromString($this->getUser()->getId()->toString()),
                ModflowId::fromString($baseModelId)
            )
        );
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
                ResultType::fromString($type),
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
                ResultType::fromString($type),
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
     * Get models latest calculation result by modelId, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get models latest calculation result by modelId, type and layerNumber.",
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
                ResultType::fromString($type),
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
     * @Rest\Route("/model/calculation/{calculationId}/result/type/{type}/layer/{layer}/totim/{totim}", methods={"GET"}, requirements={"_format"="json"})
     * @param $calculationId
     * @param $type
     * @param $layer
     * @param $totim
     * @return JsonResponse
     * @throws InvalidUuidException
     */
    public function getScenarioAnalysisResultByCalculationResultTypeAndLayerAction($calculationId, $type, $layer, $totim)
    {
        if (! Uuid::isValid($calculationId)){
            throw new InvalidUuidException();
        }

        $result = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculationId),
                ResultType::fromString($type),
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

    /**
     * Get calculation result as image by calculationId, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get calculation result as image by calculationId, type and layerNumber.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @Rest\Route("/model/{modelId}/calculation/result/type/{type}/layer/{layer}/totim/{totim}", methods={"GET"}, requirements={"_format"="png"})
     *
     * @param ParamFetcher $paramFetcher
     * @param $modelId
     * @param $type
     * @param $layer
     * @param $totim
     * @return Response
     * @throws InvalidUuidException
     *
     * @Rest\QueryParam(name="max", default=null, description="Value of the spectrum maximum")
     * @Rest\QueryParam(name="min", default=null, description="Value of the spectrum minimum")
     * @Rest\QueryParam(name="upper", requirements="\d+", default=95, description="Percentile spectrum max")
     * @Rest\QueryParam(name="loper", requirements="\d+", default=5, description="Percentile spectrum min")
     */
    public function getScenarioAnalysisResultImageByModelIdTypeLayerAndTotimAction(ParamFetcher $paramFetcher, $modelId, $type, $layer, $totim): Response
    {

        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        $calculation = $this->get('inowas.modflow_projection.calculation_list_finder')
            ->findLastCalculationByModelId(ModflowId::fromString($modelId));

        $result = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculation['calculation_id']),
                ResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );


        $heatMap = new HeatMap();
        if ($paramFetcher->get('min') && $paramFetcher->get('max')){
            $file = $heatMap->createWithAbsoluteLimits($result->data()->toArray(), (float)$paramFetcher->get('min'), (float)$paramFetcher->get('max'));
        } else {
            $file = $heatMap->createWithPercentileLimits($result->data()->toArray(), (float)$paramFetcher->get('loper'), (float)$paramFetcher->get('upper'));
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent(file_get_contents($file));
        return $response;
    }

    /**
     * Get calculation result as image by calculationId, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get calculation result as image by calculationId, type and layerNumber.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @Rest\Route("/model/calculation/{calculationId}/result/type/{type}/layer/{layer}/totim/{totim}", methods={"GET"}, requirements={"_format"="png"})
     *
     * @param ParamFetcher $paramFetcher
     * @param $calculationId
     * @param $type
     * @param $layer
     * @param $totim
     * @return Response
     * @throws InvalidUuidException
     *
     * @Rest\QueryParam(name="max", default=null, description="Value of the spectrum maximum")
     * @Rest\QueryParam(name="min", default=null, description="Value of the spectrum minimum")
     * @Rest\QueryParam(name="upper", requirements="\d+", default=95, description="Percentile spectrum max")
     * @Rest\QueryParam(name="loper", requirements="\d+", default=5, description="Percentile spectrum min")
     */
    public function getScenarioAnalysisResultImageByCalculationIdTypeLayerAndTotimAction(ParamFetcher $paramFetcher, $calculationId, $type, $layer, $totim): Response
    {

        if (! Uuid::isValid($calculationId)){
            throw new InvalidUuidException();
        }

        $result = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculationId),
                ResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );


        $heatMap = new HeatMap();
        if ($paramFetcher->get('min') && $paramFetcher->get('max')){
            $file = $heatMap->createWithAbsoluteLimits($result->data()->toArray(), (float)$paramFetcher->get('min'), (float)$paramFetcher->get('max'));
        } else {
            $file = $heatMap->createWithPercentileLimits($result->data()->toArray(), (float)$paramFetcher->get('loper'), (float)$paramFetcher->get('upper'));
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent(file_get_contents($file));
        return $response;
    }

    /**
     * Get difference of two models latest calculation result by modelIds, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "* Get difference of two models latest calculation result by modelIds, type and layerNumber.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/result/difference/models/{modelIdFirstModel}/{modelIdSecondModel}/type/{type}/layer/{layer}/totim/{totim}")
     * @param $modelIdFirstModel
     * @param $modelIdSecondModel
     * @param $type
     * @param $layer
     * @param $totim
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisResultDifferenceByModelIdsTypeLayerAndTotimAction($modelIdFirstModel, $modelIdSecondModel, $type, $layer, $totim):JsonResponse
    {
        if (! Uuid::isValid($modelIdFirstModel)){
            throw new InvalidUuidException();
        }

        $calculationFirstModel = $this->get('inowas.modflow_projection.calculation_list_finder')
            ->findLastCalculationByModelId(ModflowId::fromString($modelIdFirstModel));

        $resultFirstModel = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculationFirstModel['calculation_id']),
                ResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );

        if (! Uuid::isValid($modelIdSecondModel)){
            throw new InvalidUuidException();
        }

        $calculationSecondModel = $this->get('inowas.modflow_projection.calculation_list_finder')
            ->findLastCalculationByModelId(ModflowId::fromString($modelIdSecondModel));

        $resultSecondModel = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculationSecondModel['calculation_id']),
                ResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );

        $result = $this->calculateDifferenceResults($resultFirstModel, $resultSecondModel);

        return new JsonResponse($result);
    }

    /**
     * Get difference of two calculation results by calculationIds, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get difference of two calculation results by calculationIds, type and layerNumber.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/result/difference/calculations/{calculationIdFirstModel}/{calculationIdSecondModel}/type/{type}/layer/{layer}/totim/{totim}")
     * @param $calculationIdFirstModel
     * @param calculationIdSecondModel
     * @param $type
     * @param $layer
     * @param $totim
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisResultDifferenceByCalculationIdsTypeLayerAndTotimAction($calculationIdFirstModel, $calculationIdSecondModel, $type, $layer, $totim):JsonResponse
    {
        if (! Uuid::isValid($calculationIdFirstModel)){
            throw new InvalidUuidException();
        }

        $resultFirstModel = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculationIdFirstModel),
                ResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );

        if (! Uuid::isValid($calculationIdSecondModel)){
            throw new InvalidUuidException();
        }

        $resultSecondModel = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculationIdSecondModel),
                ResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );

        $result = $this->calculateDifferenceResults($resultFirstModel, $resultSecondModel);

        return new JsonResponse($result);
    }

    /**
     * Get timeseries of latest calculation of model by modelId, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get difference of two calculation results by calculationIds, type and layerNumber.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/result/timeseries/model/{modelId}/type/{type}/layer{layer}/nx/{nx}/ny/{ny}")
     * api/scenarioanalysis.json
     * @param $modelId
     * @param $type
     * @param $layer
     * @param $nx
     * @param $ny
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisResultTimeseriesByModelIdTypeLayerNxNyAction($modelId, $type, $layer, $nx, $ny): JsonResponse
    {
        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        $modelId = ModflowId::fromString($modelId);
        $type = ResultType::fromString($type);
        $layer = LayerNumber::fromInteger((int)$layer);
        $column = ColumnNumber::fromInteger((int)$nx);
        $row = RowNumber::fromInteger((int)$ny);

        $calculation = $this->get('inowas.modflow_projection.calculation_list_finder')
            ->findLastCalculationByModelId($modelId);

        $calculationId = ModflowId::fromString($calculation['calculation_id']);

        $timesSeries = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findTimeSeries($calculationId, $type, $layer, $column, $row);

        return new JsonResponse($timesSeries);
    }

    /**
     * Get timeseries of calculation by calculationId, type and layerNumber.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get difference of two calculation results by calculationIds, type and layerNumber.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/result/timeseries/model/{modelId}/type/{type}/layer{layer}/nx/{nx}/ny/{ny}")
     * api/scenarioanalysis.json
     * @param $calculationId
     * @param $type
     * @param $layer
     * @param $nx
     * @param $ny
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisResultTimeseriesByCalculationIdTypeLayerNxNyAction($calculationId, $type, $layer, $nx, $ny): JsonResponse
    {

        if (! Uuid::isValid($calculationId)){
            throw new InvalidUuidException();
        }

        $calculationId = ModflowId::fromString($calculationId);
        $type = ResultType::fromString($type);
        $layer = LayerNumber::fromInteger((int)$layer);
        $column = ColumnNumber::fromInteger((int)$nx);
        $row = RowNumber::fromInteger((int)$ny);

        $timesSeries = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findTimeSeries($calculationId, $type, $layer, $column, $row);

        return new JsonResponse($timesSeries);
    }

    private function calculateDifferenceResults(CalculatedResult $res1, CalculatedResult $res2): CalculatedResult
    {
        $arr1 = $res1->data()->toArray();
        $arr2 = $res2->data()->toArray();
        if (! (count($arr1) == count($arr2) && count($arr1[0]) == count($arr2[0]))){
            throw new \Exception('Arrays not in the same range');
        }

        if ($res1->type()->toString() != $res2->type()->toString()) {
            throw new \Exception('Type is not the same');
        }

        if ($res1->totalTime()->toInteger() != $res2->totalTime()->toInteger()) {
            throw new \Exception('Type is not the same');
        }

        if ($res1->layerNumber()->toInteger() != $res2->layerNumber()->toInteger()) {
            throw new \Exception('LayerNumber is not the same');
        }

        $result = [];
        foreach ($arr1 as $rowNumber => $row){
            foreach ($row as $colNumber => $value){
                if (is_null($arr1[$rowNumber][$colNumber]) || is_null($arr2[$rowNumber][$colNumber])){
                    $result[$rowNumber][$colNumber] = null;
                    continue;
                }

                $result[$rowNumber][$colNumber] = round($arr1[$rowNumber][$colNumber] - $arr2[$rowNumber][$colNumber], 3);
            }
        }

        return CalculatedResult::fromParameters(
            $res1->type(), $res1->totalTime(), $res1->layerNumber(), HeadData::from2dArray($result)
        );
    }
}
