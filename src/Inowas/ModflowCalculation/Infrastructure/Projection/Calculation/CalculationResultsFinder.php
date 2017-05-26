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

    public function findTimesByModelId(ModflowId $modelId, ResultType $type, LayerNumber $layerNumber)
    {
        $calculationId = $this->connection->fetchColumn(
            sprintf('SELECT calculation_id from %s WHERE model_id = :model_id ORDER BY id DESC LIMIT 1', Table::CALCULATION_LIST),
            ['model_id' => $modelId->toString()]
        );

        if ($calculationId == false){
            return [];
        }

        return $this->findTimes(ModflowId::fromString($calculationId), $type, $layerNumber);
    }

    public function getTotalTimesFromCalculationById(ModflowId $calculationId): ?TotalTimes
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT start as start_date_time, time_unit, heads, drawdowns from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_RESULTS),
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

    public function findTimes(ModflowId $calculationId, ResultType $type, LayerNumber $layerNumber): array
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

    public function findValue(ModflowId $calculationId, ResultType $type, LayerNumber $layerNumber, TotalTime $totalTime): HeadData
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
}
