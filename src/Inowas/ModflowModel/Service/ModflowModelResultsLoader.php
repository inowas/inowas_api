<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Service;

use Inowas\Common\Calculation\HeadData;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\Ncol;
use Inowas\Common\Grid\Nrow;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\TimeSeriesData;
use Inowas\ModflowModel\Model\AMQP\ModflowReadDataRequest;
use Inowas\ModflowModel\Model\AMQP\ModflowReadDataResponse;

class ModflowModelResultsLoader
{
    /** @var  AMQPRemoteProcedureCall */
    protected $rpcClient;

    public function __construct(AMQPRemoteProcedureCall $rpcClient)
    {
        $this->rpcClient = $rpcClient;
    }

    /**
     * @param CalculationId $calculationId
     * @return array
     */
    public function getFileList(CalculationId $calculationId): array
    {
        $request = ModflowReadDataRequest::forFileList($calculationId);
        $response = ModflowReadDataResponse::fromJson($this->rpcClient->send($request));
        return $response->data();
    }

    /**
     * @param CalculationId $calculationId
     * @param Extension $extension
     * @return string
     */
    public function getFile(CalculationId $calculationId, Extension $extension): string
    {
        $request = ModflowReadDataRequest::forFile($calculationId, $extension);
        $response = ModflowReadDataResponse::fromJson($this->rpcClient->send($request));
        return $response->data()[0];
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param CalculationId $calculationId
     * @param ResultType $type
     * @param LayerNumber $layerNumber
     * @param TotalTime $totalTime
     * @return HeadData
     */
    public function findHeadData(CalculationId $calculationId, ResultType $type, LayerNumber $layerNumber, TotalTime $totalTime): HeadData
    {
        $request = ModflowReadDataRequest::forLayerData(
            $calculationId,
            $type,
            $totalTime,
            $layerNumber
        );

        $response = ModflowReadDataResponse::fromJson($this->rpcClient->send($request));
        return HeadData::from2dArray($response->data());
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param CalculationId $calculationId
     * @param CalculationId $calculationId2
     * @param ResultType $type
     * @param LayerNumber $layerNumber
     * @param TotalTime $totalTime
     * @return HeadData
     * @throws \RuntimeException
     */
    public function findHeadDifference(CalculationId $calculationId, CalculationId $calculationId2, ResultType $type, LayerNumber $layerNumber, TotalTime $totalTime): HeadData
    {
        $request = ModflowReadDataRequest::forLayerData(
            $calculationId,
            $type,
            $totalTime,
            $layerNumber
        );

        $response = ModflowReadDataResponse::fromJson($this->rpcClient->send($request));
        $result1 = HeadData::from2dArray($response->data());

        $request = ModflowReadDataRequest::forLayerData(
            $calculationId2,
            $type,
            $totalTime,
            $layerNumber
        );

        $response = ModflowReadDataResponse::fromJson($this->rpcClient->send($request));
        $result2 = HeadData::from2dArray($response->data());

        return $this->calculateDifferenceResults($result1, $result2);
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param CalculationId $calculationId
     * @param ResultType $type
     * @param LayerNumber $layerNumber
     * @param Nrow $ny
     * @param Ncol $nx
     * @return TimeSeriesData
     */
    public function findTimeSeries(CalculationId $calculationId, ResultType $type, LayerNumber $layerNumber, Nrow $ny, Ncol $nx): TimeSeriesData
    {
        $request = ModflowReadDataRequest::forTimeSeries(
            $calculationId,
            $type,
            $layerNumber,
            $ny,
            $nx
        );

        $response = ModflowReadDataResponse::fromJson($this->rpcClient->send($request));
        return TimeSeriesData::fromArray($response->data());
    }

    /**
     * @param HeadData $res1
     * @param HeadData $res2
     * @return HeadData
     * @throws \RuntimeException
     */
    private function calculateDifferenceResults(HeadData $res1, HeadData $res2): HeadData
    {
        $arr1 = $res1->toArray();
        $arr2 = $res2->toArray();

        if (!(\count($arr1) === \count($arr2) && \count($arr1[0]) === \count($arr2[0]))) {
            throw new \RuntimeException('Arrays not in the same range');
        }

        $result = [];
        /** @var array $row */
        foreach ($arr1 as $rowNumber => $row) {
            foreach ($row as $colNumber => $value) {
                if (null === $arr1[$rowNumber][$colNumber] || null === $arr2[$rowNumber][$colNumber]) {
                    $result[$rowNumber][$colNumber] = null;
                    continue;
                }

                $result[$rowNumber][$colNumber] = round($arr1[$rowNumber][$colNumber] - $arr2[$rowNumber][$colNumber], 3);
            }
        }

        return HeadData::from2dArray($result);
    }
}
