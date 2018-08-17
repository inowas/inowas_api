<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Optimization;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\OptimizationCalculationWasCanceled;
use Inowas\ModflowModel\Model\Event\OptimizationCalculationWasStarted;
use Inowas\ModflowModel\Model\Event\OptimizationProgressWasUpdated;
use Inowas\ModflowModel\Model\Event\OptimizationInputWasUpdated;

class OptimizationProjector extends AbstractDoctrineConnectionProjector
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::OPTIMIZATIONS);
        $table->addColumn('model_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('input', 'text', ['default' => '[]']);
        $table->addColumn('progress', 'text', ['default' => '[]']);
        $table->addColumn('solutions', 'text', ['default' => '[]']);
        $table->addColumn('state', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['model_id']);
        $table->addIndex(['model_id']);
        $this->addSchema($schema);
    }

    public function onOptimizationProgressWasUpdated(OptimizationProgressWasUpdated $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id', Table::OPTIMIZATIONS),
            ['model_id' => $event->modelId()->toString()]
        );

        if ($result && $result['count'] > 0) {
            $this->connection->update(Table::OPTIMIZATIONS,
                [
                    'progress' => $event->progress()->toJson(),
                    'solutions' => $event->solutions()->toJson(),
                    'state' => $event->state()->toInt()
                ],
                ['model_id' => $event->modelId()->toString()]
            );
        }
    }

    public function onOptimizationInputWasUpdated(OptimizationInputWasUpdated $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id', Table::OPTIMIZATIONS),
            ['model_id' => $event->modelId()->toString()]
        );

        if ($result['count'] === 0) {
            $this->connection->insert(Table::OPTIMIZATIONS, [
                'model_id' => $event->modelId()->toString(),
                'input' => $event->input()->toJson()
            ]);
        }

        if ($result['count'] === 1) {
            $this->connection->update(Table::OPTIMIZATIONS,
                ['input' => $event->input()->toJson()],
                ['model_id' => $event->modelId()->toString()]
            );
        }
    }

    public function onOptimizationCalculationWasStarted(OptimizationCalculationWasStarted $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id', Table::OPTIMIZATIONS),
            ['model_id' => $event->modelId()->toString()]
        );

        if ($result && $result['count'] > 0) {
            $this->connection->update(Table::OPTIMIZATIONS,
                ['state' => OptimizationState::STARTED],
                ['model_id' => $event->modelId()->toString()]
            );
        }
    }

    public function onOptimizationCalculationWasCanceled(OptimizationCalculationWasCanceled $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id', Table::OPTIMIZATIONS),
            ['model_id' => $event->modelId()->toString()]
        );

        if ($result && $result['count'] > 0) {
            $this->connection->update(Table::OPTIMIZATIONS,
                ['state' => OptimizationState::CANCELLED],
                ['model_id' => $event->modelId()->toString()]
            );
        }
    }
}
