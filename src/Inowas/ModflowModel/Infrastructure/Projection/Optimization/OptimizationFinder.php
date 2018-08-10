<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Optimization;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Optimization;
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
     * @param ModflowId $modelId
     * @return Optimization|null
     */
    public function getOptimization(ModflowId $modelId): ?Optimization
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT optimization FROM %s WHERE model_id = :model_id', Table::OPTIMIZATIONS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false || \array_key_exists('optimization', $result) === false) {
            return null;
        }

        return Optimization::fromDB($result['optimization']);
    }
}
