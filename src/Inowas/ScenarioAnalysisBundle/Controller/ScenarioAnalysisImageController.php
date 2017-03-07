<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use HeatMap\HeatMap;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\DateTime\TotalTime;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidUuidException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

class ScenarioAnalysisImageController extends FOSRestController
{
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

        $headData = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculation['calculation_id']),
                ResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );


        $heatMap = new HeatMap();
        if ($paramFetcher->get('min') && $paramFetcher->get('max')){
            $file = $heatMap->createWithAbsoluteLimits($headData->toArray(), (float)$paramFetcher->get('min'), (float)$paramFetcher->get('max'));
        } else {
            $file = $heatMap->createWithPercentileLimits($headData->toArray(), (float)$paramFetcher->get('loper'), (float)$paramFetcher->get('upper'));
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

        $headData = $this->get('inowas.modflow_projection.calculation_results_finder')
            ->findValue(
                ModflowId::fromString($calculationId),
                ResultType::fromString($type),
                LayerNumber::fromInteger((int)$layer),
                TotalTime::fromInt((int)$totim)
            );


        $heatMap = new HeatMap();
        if ($paramFetcher->get('min') && $paramFetcher->get('max')){
            $file = $heatMap->createWithAbsoluteLimits($headData->toArray(), (float)$paramFetcher->get('min'), (float)$paramFetcher->get('max'));
        } else {
            $file = $heatMap->createWithPercentileLimits($headData->toArray(), (float)$paramFetcher->get('loper'), (float)$paramFetcher->get('upper'));
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent(file_get_contents($file));
        return $response;
    }
}
