<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Optimization;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\OptimizationCalculationWasCanceled;
use Inowas\ModflowModel\Model\Event\OptimizationCalculationWasStarted;
use Inowas\ModflowModel\Model\Event\OptimizationWasUpdated;


class OptimizationProjector extends AbstractDoctrineConnectionProjector
{
    public const STARTED_BY_USER = 1;
    public const CALCULATING = 2;
    public const FINISHED = 2;
    public const CANCELED_BY_USER = 11;
    public const STOPPED = 12;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::OPTIMIZATIONS);
        $table->addColumn('model_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('optimization', 'text', ['notnull' => false]);
        $table->addColumn('state', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['model_id']);
        $table->addIndex(['model_id']);
        $this->addSchema($schema);
    }

    public function onOptimizationWasUpdated(OptimizationWasUpdated $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id', Table::BOUNDARIES),
            ['model_id' => $event->modelId()->toString()]
        );

        if ($result && $result['count'] > 0) {
            $this->connection->update(Table::BOUNDARIES,
                ['optimization' => $event->optimization()->toJson()],
                ['model_id' => $event->modelId()->toString()]
            );
        }

        $this->connection->insert(Table::BOUNDARIES, array(
            'model_id' => $event->modelId()->toString(),
            'optimization' => $event->optimization()->toJson()
        ));
    }

    public function onOptimizationCalculationWasStarted(OptimizationCalculationWasStarted $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id', Table::BOUNDARIES),
            ['model_id' => $event->modelId()->toString()]
        );

        if ($result && $result['count'] > 0) {
            $this->connection->update(Table::BOUNDARIES,
                ['state' => self::STARTED_BY_USER],
                ['model_id' => $event->modelId()->toString()]
            );
        }
    }

    public function onOptimizationCalculationWasCanceled(OptimizationCalculationWasCanceled $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id', Table::BOUNDARIES),
            ['model_id' => $event->modelId()->toString()]
        );

        if ($result && $result['count'] > 0) {
            $this->connection->update(Table::BOUNDARIES,
                ['state' => self::CANCELED_BY_USER],
                ['model_id' => $event->modelId()->toString()]
            );
        }
    }
}
