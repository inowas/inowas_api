<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use HeatMap\HeatMap;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\DateTime\TotalTime;
use Inowas\ModflowBundle\Exception\InvalidUuidException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Symfony\Component\HttpFoundation\Response;

class ModflowCalculationImageController extends InowasRestController
{
    /**
     * Get calculation result as image by calculationId, type, layerNumber and totim.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get calculation result as image by calculationId, type, layerNumber and totim.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @Rest\Route("/calculations/{id}/results/types/{type}/layers/{layer}/totims/{totim}", methods={"GET"}, requirements={"_format"="png"})
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
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
    public function getCalculationImageHeadResultsByTypeLayerAndTotimAction(ParamFetcher $paramFetcher, string $id, string $type, string $layer, string $totim): Response
    {

        $this->assertUuidIsValid($id);
        $calculationId = ModflowId::fromString($id);

        $type = ResultType::fromString($type);
        $layerNumber = LayerNumber::fromInteger((int)$layer);
        $totim = TotalTime::fromInt((int)$totim);

        $headData = $this->get('inowas.modflowcalculation.calculation_results_finder')->findHeadValue(
            $calculationId,
            $type,
            $layerNumber,
            $totim
        );

        #$spectrum = array('purple', 'red', 'yellow', 'lime', 'aqua', 'blue');
        $spectrum = array('#800080', '#FF0000', '#FFFF00', '#00FF00', '#00FFFF', '#0000FF');
        $heatMap = new HeatMap($spectrum);
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
     * Get calculation headValues difference image by calculationIds, type, layernumber, totim.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get calculation headValues difference image by calculationIds, type, layernumber, totim.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @Rest\Route("/calculations/{id}/results/difference/{id2}/types/{type}/layers/{layer}/totims/{totim}", methods={"GET"}, requirements={"_format"="png"})
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     * @param $id2
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
    public function getScenarioAnalysisResultDifferenceImageByModelIdsTypeLayerAndTotimAction(ParamFetcher $paramFetcher, $id, $id2, $type, $layer, $totim): Response
    {

        $this->assertUuidIsValid($id);
        $calculationId = ModflowId::fromString($id);

        $this->assertUuidIsValid($id);
        $calculationId2 = ModflowId::fromString($id2);

        $type = ResultType::fromString($type);
        $layerNumber = LayerNumber::fromInteger((int)$layer);
        $totim = TotalTime::fromInt((int)$totim);

        $headData = $this->get('inowas.modflowcalculation.calculation_results_finder')->findHeadDifference(
            $calculationId,
            $calculationId2,
            $type,
            $layerNumber,
            $totim
        );

        #$spectrum = array('purple', 'red', 'yellow', 'lime', 'aqua', 'blue');
        $spectrum = array('#800080', '#FF0000', '#FFFF00', '#00FF00', '#00FFFF', '#0000FF');
        $heatMap = new HeatMap($spectrum);
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