<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Calculation\CalculationMessage;
use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Calculation\CalculationStateQuery;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\LayerValues;
use Inowas\Common\Modflow\Results;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TotalTimes;
use Inowas\Common\Id\CalculationId;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class ModflowCalculationFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function getNextModflowModelIdToCalculate(): ?ModflowId
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT model_id FROM %s WHERE state = :state ORDER BY updated_at ASC LIMIT 1', Table::MODELS_CALCULATIONS),
            ['state' => CalculationState::CALCULATION_PROCESS_STARTED]
        );

        if ($result === false) {
            return null;
        }

        return ModflowId::fromString($result['model_id']);
    }

    public function getModelsCalculationsDetailsByModelId(ModflowId $modelId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::MODELS_CALCULATIONS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false) {
            return null;
        }

        return $result;
    }

    public function getCalculationStateQueryByModelId(ModflowId $modelId): ?CalculationStateQuery
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT calculation_id, state FROM %s WHERE model_id = :model_id', Table::MODELS_CALCULATIONS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false) {
            return null;
        }

        $calculationState = CalculationState::fromInt($result['state']);

        if (null === $result['calculation_id']) {
            return CalculationStateQuery::createWithEmptyCalculationId($calculationState);
        }

        $calculationId = CalculationId::fromString($result['calculation_id']);
        if ($calculationState->toInt() < CalculationState::CALCULATION_FINISHED) {
            return CalculationStateQuery::createWithCalculationId(
                $calculationId, $calculationState, CalculationMessage::fromString('')
            );
        }

        return $this->getCalculationStateQuery($calculationId);
    }

    private function getCalculationStateQuery(CalculationId $calculationId): ?CalculationStateQuery
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT calculation_id, state, message FROM %s WHERE calculation_id = :calculation_id', Table::CALCULATIONS),
            array('calculation_id' => $calculationId->toString())
        );

        if ($result === false) {
            return null;
        }

        return CalculationStateQuery::createWithCalculationId(
            $calculationId,
            CalculationState::fromInt($result['state']),
            CalculationMessage::fromString($result['message'])
        );
    }

    /**
     * @param CalculationId $calculationId
     * @return array|null
     * @throws \Exception
     */
    public function getCalculationDetailsById(CalculationId $calculationId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT calculation_id, state, message FROM %s WHERE calculation_id = :calculation_id', Table::CALCULATIONS),
            array('calculation_id' => $calculationId->toString())
        );

        if ($result === false) {
            return null;
        }
        $result['times'] = $this->getTotalTimesFromCalculationById($calculationId);
        $result['layer_values'] = $this->findLayerValues($calculationId);

        return $result;
    }

    /**
     * @param CalculationId $calculationId
     * @return Results|null
     * @throws \Exception
     */
    public function getCalculationResults(CalculationId $calculationId): ?Results
    {
        $layerValues = $this->findLayerValues($calculationId);
        if (!$layerValues instanceof LayerValues) {
            return null;
        }

        $totalTimes = $this->getTotalTimesFromCalculationById($calculationId);
        if (!$totalTimes instanceof TotalTimes) {
            return null;
        }

        return Results::create($calculationId, $layerValues, $totalTimes);
    }

    public function getTimesByModelId(ModflowId $modelId, ResultType $type): array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT calculation_id from %s WHERE model_id = :model_id ORDER BY id DESC LIMIT 1', Table::CALCULATIONS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false) {
            return [];
        }

        return $this->getTimes(CalculationId::fromString($result['calculation_id']), $type);
    }

    /**
     * @param CalculationId $calculationId
     * @return TotalTimes|null
     * @throws \Exception
     */
    public function getTotalTimesFromCalculationById(CalculationId $calculationId): ?TotalTimes
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT stressperiods from %s WHERE calculation_id = :calculation_id', Table::MODFLOWMODELS),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false) {
            return null;
        }

        $stressPeriods = StressPeriods::createFromJson($result['stressperiods']);
        $startDateTime = $stressPeriods->start();
        $timeUnit = $stressPeriods->timeUnit();

        $result = $this->connection->fetchAssoc(
            sprintf('SELECT heads, drawdowns from %s WHERE calculation_id = :calculation_id', Table::CALCULATIONS),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false) {
            return null;
        }

        $heads = json_decode($result['heads'], true);
        $drawdowns = json_decode($result['drawdowns'], true);

        $times = [];
        if (\count($heads) > 0) {
            $times = $heads;
        }
        if (\count($drawdowns) > 0) {
            $times = $drawdowns;
        }
        return TotalTimes::create($startDateTime, $timeUnit, $times);
    }

    public function getTimes(CalculationId $calculationId, ResultType $type): array
    {

        $row = $this->connection->fetchAssoc(
            sprintf('SELECT * from %s WHERE calculation_id = :calculation_id', Table::CALCULATIONS),
            ['calculation_id' => $calculationId->toString()]
        );


        $totims = json_decode($row[$type->toString() . 's']);
        $result = [];
        foreach ($totims as $totim) {
            $result[] = TotalTime::fromInt($totim);
        }

        return $result;
    }

    public function findLayerValues(CalculationId $calculationId): ?LayerValues
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT number_of_layers, concentrations, drawdowns, heads from %s WHERE calculation_id = :calculation_id', Table::CALCULATIONS),
            ['calculation_id' => $calculationId->toString()]
        );

        $numberOfLayers = $result['number_of_layers'];
        $concentrations = json_decode($result['concentrations'], true);
        $drawdowns = json_decode($result['drawdowns'], true);
        $heads = json_decode($result['heads'], true);

        $result = [];

        /** @noinspection ForeachInvariantsInspection */
        for ($l = 0; $l < $numberOfLayers; $l++) {
            $result[$l] = [];
            if (\count($heads) > 0) {
                $result[$l][] = ResultType::HEAD_TYPE;
            }

            if (\count($drawdowns) > 0) {
                $result[$l][] = ResultType::DRAWDOWN_TYPE;
            }

            if (\count($concentrations) > 0) {
                $result[$l][] = ResultType::CONCENTRATION_TYPE;
            }
        }

        return LayerValues::fromArray($result);
    }
}
