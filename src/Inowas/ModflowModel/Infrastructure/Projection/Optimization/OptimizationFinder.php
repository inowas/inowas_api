<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Optimization;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Optimization;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class OptimizationFinder
{
    /** @var Connection $connection */
    protected $connection;

    /**
     * OptimizationFinder constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    /**
     * @param ModflowId $optimizationId
     * @return Optimization|null
     */
    public function getOptimizationById(ModflowId $optimizationId): ?Optimization
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE optimization_id = :optimization_id', Table::OPTIMIZATIONS),
            ['optimization_id' => $optimizationId->toString()]
        );

        if ($result === false) {
            return null;
        }

        return Optimization::fromArray([
            'input' => \json_decode($result['input'], true),
            'state' => (int)$result['state'],
            'methods' => \json_decode($result['methods'], true)
        ]);
    }

    /**
     * @param ModflowId $modelId
     * @return Optimization|null
     */
    public function getOptimizationByModelId(ModflowId $modelId): ?Optimization
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::OPTIMIZATIONS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false) {
            return null;
        }

        return Optimization::fromArray([
            'input' => \json_decode($result['input'], true),
            'state' => (int)$result['state'],
            'methods' => \json_decode($result['methods'], true)
        ]);
    }

    /**
     * @param ModflowId $optimizationId
     * @return ModflowId|null
     */
    public function getModelId(ModflowId $optimizationId): ?ModflowId
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT model_id FROM %s WHERE optimization_id = :optimization_id', Table::OPTIMIZATIONS),
            ['optimization_id' => $optimizationId->toString()]
        );

        if ($result === false) {
            return null;
        }

        return ModflowId::fromString($result['model_id']);
    }

    public function getNextOptimizationToCalculate(): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE state = :state ORDER BY updated_at ASC LIMIT 1', Table::OPTIMIZATIONS),
            ['state' => OptimizationState::STARTED]
        );

        if (\is_array($result)) {
            return $result;
        }

        return null;
    }

    public function getNextOptimizationToCancel(): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE state = :state ORDER BY updated_at ASC LIMIT 1', Table::OPTIMIZATIONS),
            ['state' => OptimizationState::CANCELLING]
        );

        if (\is_array($result)) {
            return $result;
        }

        return null;
    }
}
