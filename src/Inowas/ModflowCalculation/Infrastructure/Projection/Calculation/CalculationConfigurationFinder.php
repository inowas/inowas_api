<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Modflow\TotalTimes;
use Inowas\ModflowCalculation\Infrastructure\Projection\Table;
use Inowas\ModflowCalculation\Model\ModflowCalculationConfigurationRequest;

class CalculationConfigurationFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function findModelId(ModflowId $calculationId): ?ModflowId
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT modflow_model_id from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        return ModflowId::fromString($result['modflow_model_id']);
    }

    public function getStressPeriodsFromCalculationById(ModflowId $calculationId): ?StressPeriods
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT stress_periods from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false){
            return null;
        }

        return StressPeriods::createFromArray(json_decode($result['stress_periods'], true));
    }

    public function getTotalTimesFromCalculationById(ModflowId $calculationId): ?TotalTimes
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT start as start_date_time, time_unit from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false){
            return null;
        }

        $startDateTime = DateTime::fromAtom($result['start_date_time']);
        $timeUnit = TimeUnit::fromInt($result['time_unit']);

        $result = $this->connection->fetchAssoc(
            sprintf('SELECT heads, drawdowns from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_RESULTS),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false){
            return null;
        }

        $heads = json_decode($result['heads'], true);
        $drawdowns = json_decode($result['drawdowns'], true);

        $times = [];
        if (count($heads) > 0){$times = $heads;}
        if (count($drawdowns) > 0){$times = $drawdowns;}
        return TotalTimes::create($startDateTime, $timeUnit, $times);
    }

    public function getFlopyCalculation(ModflowId $calculationId): ?ModflowCalculationConfigurationRequest
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result == false){
            return null;
        }

        return ModflowCalculationConfigurationRequest::fromData((array)json_decode($result['configuration']));
    }

    public function getConfigurationJson(ModflowId $calculationId): ?string
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result == false){
            return null;
        }

        return $result['configuration'];
    }

    public function findAll(): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * from %s', Table::CALCULATION_CONFIG)
        );
    }
}
