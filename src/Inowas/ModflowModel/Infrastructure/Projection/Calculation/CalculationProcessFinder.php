<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class CalculationProcessFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function getNextRowWithState(CalculationState $state): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * from %s WHERE state = :state ORDER BY id ASC LIMIT 1', Table::CALCULATION_PROCESSES),
            ['state' => $state->toInt()]
        );
    }

    public function getNextRow(): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * from %s ORDER BY id ASC LIMIT 1', Table::CALCULATION_PROCESSES)
        );
    }

    /**
     * @param ModflowId $modelId
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function deleteByModelId(ModflowId $modelId): void
    {
        $this->connection->delete(Table::CALCULATION_PROCESSES,
            ['model_id' => $modelId->toString()]
        );
    }
}
