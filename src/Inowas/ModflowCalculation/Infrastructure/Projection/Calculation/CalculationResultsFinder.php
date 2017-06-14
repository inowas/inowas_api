<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Calculation\HeadData;
use Inowas\Common\Calculation\TimeSeriesData;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\Ncol;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\Nrow;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Modflow\LayerValues;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Modflow\TotalTimes;
use Inowas\ModflowCalculation\Infrastructure\Projection\Table;
use Inowas\ModflowCalculation\Model\ModflowCalculationReadDataRequest;
use Inowas\ModflowCalculation\Model\ModflowCalculationReadDataResponse;
use Inowas\ModflowCalculation\Service\AMQPModflowCalculationReadData;

class CalculationResultsFinder
{
    /** @var Connection $connection */
    protected $connection;

    /** @var  AMQPModflowCalculationReadData */
    protected $reader;

    public function __construct(Connection $connection, AMQPModflowCalculationReadData $reader) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $this->reader = $reader;
    }

    public function findTimesByModelId(ModflowId $modelId, ResultType $type): array
    {
        $calculationId = $this->connection->fetchColumn(
            sprintf('SELECT calculation_id from %s WHERE model_id = :model_id ORDER BY id DESC LIMIT 1', Table::CALCULATION_LIST),
            ['model_id' => $modelId->toString()]
        );

        if ($calculationId === false){
            return [];
        }

        return $this->findTimes(ModflowId::fromString($calculationId), $type);
    }

    public function getTotalTimesFromCalculationById(ModflowId $calculationId): ?TotalTimes
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT start_date_time, time_unit, heads, drawdowns from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_RESULTS),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false){
            return null;
        }

        $startDateTime = DateTime::fromAtom($result['start_date_time']);
        $timeUnit = TimeUnit::fromInt($result['time_unit']);
        $heads = json_decode($result['heads'], true);
        $drawdowns = json_decode($result['drawdowns'], true);

        $times = [];
        if (count($heads) > 0){$times = $heads;}
        if (count($drawdowns) > 0){$times = $drawdowns;}
        return TotalTimes::create($startDateTime, $timeUnit, $times);
    }

    public function findTimes(ModflowId $calculationId, ResultType $type): array
    {

        $row = $this->connection->fetchAssoc(
            sprintf('SELECT * from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_RESULTS),
            ['calculation_id' => $calculationId->toString()]
        );


        $totims = json_decode($row[$type->toString().'s']);
        $result = [];
        foreach ($totims as $totim){
            $result[] = TotalTime::fromInt($totim);
        }

        return $result;
    }

    public function findLayerValues(ModflowId $calculationId): ?LayerValues
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT number_of_layers, heads, drawdowns from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_RESULTS),
            ['calculation_id' => $calculationId->toString()]
        );

        $numberOfLayers = $result['number_of_layers'];
        $drawdowns = $result['drawdowns'];
        $heads = $result['heads'];

        $result = [];

        for ($l = 0; $l<$numberOfLayers; $l++) {
            if (count($drawdowns)>0){
                $result[$l][] =  ResultType::DRAWDOWN_TYPE;
            }

            if (count($heads)>0){
                $result[$l][] =  ResultType::HEAD_TYPE;
            }
        }

        return LayerValues::fromArray($result);
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $calculationId
     * @param ResultType $type
     * @param LayerNumber $layerNumber
     * @param TotalTime $totalTime
     * @return HeadData
     */
    public function findHeadValue(ModflowId $calculationId, ResultType $type, LayerNumber $layerNumber, TotalTime $totalTime): HeadData
    {
        $request = ModflowCalculationReadDataRequest::forLayerData(
            $calculationId,
            $type,
            $totalTime,
            $layerNumber
        );

        $response = ModflowCalculationReadDataResponse::fromJson($this->reader->read($request));
        return HeadData::from2dArray($response->data());
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $calculationId
     * @param ModflowId $calculationId2
     * @param ResultType $type
     * @param LayerNumber $layerNumber
     * @param TotalTime $totalTime
     * @return HeadData
     */
    public function findHeadDifference(ModflowId $calculationId, ModflowId $calculationId2, ResultType $type, LayerNumber $layerNumber, TotalTime $totalTime): HeadData
    {
        $request = ModflowCalculationReadDataRequest::forLayerData(
            $calculationId,
            $type,
            $totalTime,
            $layerNumber
        );

        $response = ModflowCalculationReadDataResponse::fromJson($this->reader->read($request));
        $result1 = HeadData::from2dArray($response->data());

        $request = ModflowCalculationReadDataRequest::forLayerData(
            $calculationId2,
            $type,
            $totalTime,
            $layerNumber
        );

        $response = ModflowCalculationReadDataResponse::fromJson($this->reader->read($request));
        $result2 = HeadData::from2dArray($response->data());

        return $this->calculateDifferenceResults($result1, $result2);
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $calculationId
     * @param ResultType $type
     * @param LayerNumber $layerNumber
     * @param Nrow $ny
     * @param Ncol $nx
     * @return TimeSeriesData
     */
    public function findTimeSeries(ModflowId $calculationId, ResultType $type, LayerNumber $layerNumber, Nrow $ny, Ncol $nx): TimeSeriesData
    {
        $request = ModflowCalculationReadDataRequest::forTimeSeries(
            $calculationId,
            $type,
            $layerNumber,
            $ny,
            $nx
        );

        $response = ModflowCalculationReadDataResponse::fromJson($this->reader->read($request));
        return TimeSeriesData::fromArray($response->data());
    }

    private function calculateDifferenceResults(HeadData $res1, HeadData $res2): HeadData
    {
        $arr1 = $res1->toArray();
        $arr2 = $res2->toArray();

        if (! (count($arr1) === count($arr2) && count($arr1[0]) === count($arr2[0]))){
            throw new \RuntimeException('Arrays not in the same range');
        }

        $result = [];
        /** @var array $row */
        foreach ($arr1 as $rowNumber => $row){
            foreach ($row as $colNumber => $value){
                if (null === $arr1[$rowNumber][$colNumber] || null === $arr2[$rowNumber][$colNumber]){
                    $result[$rowNumber][$colNumber] = null;
                    continue;
                }

                $result[$rowNumber][$colNumber] = round($arr1[$rowNumber][$colNumber] - $arr2[$rowNumber][$colNumber], 3);
            }
        }

        return HeadData::from2dArray($result);
    }
}
