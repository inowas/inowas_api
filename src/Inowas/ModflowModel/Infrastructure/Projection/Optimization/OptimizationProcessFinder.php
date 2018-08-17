<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Optimization;

use Doctrine\DBAL\Connection;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class OptimizationProcessFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function getNextRowWithState(OptimizationState $state): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * from %s WHERE state = :state ORDER BY id ASC LIMIT 1', Table::OPTIMIZATION_PROCESSES),
            ['state' => $state->toInt()]
        );
    }

    public function getNextRow(): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * from %s ORDER BY id ASC LIMIT 1', Table::OPTIMIZATION_PROCESSES)
        );
    }

    /**
     * @param int $id
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function removeById(int $id): void
    {
        $this->connection->delete(Table::OPTIMIZATION_PROCESSES, [
            'id' => $id
        ]);
    }
}
