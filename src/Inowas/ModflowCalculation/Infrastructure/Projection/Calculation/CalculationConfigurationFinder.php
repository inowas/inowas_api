<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\ModflowCalculation\Infrastructure\Projection\Table;
use Inowas\ModflowCalculation\Model\ModflowCalculationConfiguration;
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

    public function getCalculationConfiguration(ModflowId $calculationId): ?ModflowCalculationConfigurationRequest
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false){
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

        if ($result === false){
            return null;
        }

        return $result['configuration'];
    }

    public function getConfigurationByModelId(ModflowId $modelId): ?ModflowCalculationConfiguration
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration from %s WHERE modflow_model_id = :modflow_model_id', Table::CALCULATION_CONFIG),
            ['modflow_model_id' => $modelId->toString()]
        );

        if (false === $result) {
            return null;
        }

        return ModflowCalculationConfiguration::fromJson($result['configuration']);
    }

    public function findAll(): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * from %s', Table::CALCULATION_CONFIG)
        );
    }
}
