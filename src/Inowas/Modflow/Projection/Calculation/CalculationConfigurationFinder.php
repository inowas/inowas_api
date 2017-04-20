<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Projection\Table;
use Inowas\Soilmodel\Interpolation\FlopyConfiguration;

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

    public function getFlopyCalculation(ModflowId $calculationId): ?FlopyConfiguration
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result == false){
            return null;
        }

        return FlopyConfiguration::fromData((array)json_decode($result['configuration']));
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
