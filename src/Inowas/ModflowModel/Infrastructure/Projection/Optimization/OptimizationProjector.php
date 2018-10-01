<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Optimization;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Inowas\ModflowModel\Model\Event\OptimizationStateWasUpdated;
use Inowas\ModflowModel\Model\Event\OptimizationResultsWereUpdated;
use Inowas\ModflowModel\Model\Event\OptimizationInputWasUpdated;

class OptimizationProjector extends AbstractDoctrineConnectionProjector
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::OPTIMIZATIONS);
        $table->addColumn('optimization_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('model_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('input', 'text', ['default' => '[]']);
        $table->addColumn('progress', 'text', ['default' => '[]']);
        $table->addColumn('solutions', 'text', ['default' => '[]']);
        $table->addColumn('state', 'integer', ['default' => 0]);
        $table->addColumn('created_at', 'integer', ['default' => 0]);
        $table->addColumn('updated_at', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['optimization_id', 'model_id']);
        $table->addIndex(['model_id']);
        $this->addSchema($schema);
    }

    public function onOptimizationInputWasUpdated(OptimizationInputWasUpdated $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE optimization_id = :optimization_id', Table::OPTIMIZATIONS),
            ['model_id' => $event->modelId()->toString(), 'optimization_id' => $event->optimizationId()->toString()]
        );

        if ($result['count'] === 0) {
            $this->connection->insert(Table::OPTIMIZATIONS, [
                'optimization_id' => $event->optimizationId()->toString(),
                'model_id' => $event->modelId()->toString(),
                'input' => $event->input()->toJson(),
                'created_at' => $event->createdAt()->getTimestamp(),
                'updated_at' => $event->createdAt()->getTimestamp()
            ]);
        }

        if ($result['count'] === 1) {
            $this->connection->update(Table::OPTIMIZATIONS,
                [
                    'input' => $event->input()->toJson(),
                    'updated_at' => $event->createdAt()->getTimestamp()
                ],
                ['model_id' => $event->modelId()->toString(), 'optimization_id' => $event->optimizationId()->toString()]
            );
        }
    }

    public function onOptimizationStateWasUpdated(OptimizationStateWasUpdated $event): void
    {
        $this->connection->update(Table::OPTIMIZATIONS, [
            'state' => $event->state()->toInt(),
            'updated_at' => $event->createdAt()->getTimestamp()
        ], [
            'model_id' => $event->modelId()->toString(),
            'optimization_id' => $event->optimizationId()->toString()
        ]);

        if ($event->response() instanceof ModflowOptimizationResponse) {
            $this->connection->update(Table::OPTIMIZATIONS,
                [
                    'solutions' => json_encode($event->response()->solutions()->toArray()),
                    'progress' => json_encode($event->response()->progress()->toArray())
                ],
                [
                    'model_id' => $event->modelId()->toString(),
                    'optimization_id' => $event->optimizationId()->toString()
                ]
            );
        }

        if ($event->state()->toInt() === OptimizationState::STARTED) {
            $this->connection->update(Table::OPTIMIZATIONS,
                ['solutions' => json_encode([]), 'progress' => json_encode([])],
                [
                    'model_id' => $event->modelId()->toString(),
                    'optimization_id' => $event->optimizationId()->toString()
                ]
            );
        }

    }

    public function onOptimizationResultsWereUpdated(OptimizationResultsWereUpdated $event): void
    {

        if ($event->solutions()->count() > 0) {
            $this->connection->update(Table::OPTIMIZATIONS,
                [
                    'progress' => $event->progress()->toJson(),
                    'solutions' => $event->solutions()->toJson(),
                    'state' => $event->state()->toInt(),
                    'updated_at' => $event->createdAt()->getTimestamp()
                ],
                ['model_id' => $event->modelId()->toString(), 'optimization_id' => $event->optimizationId()->toString()]
            );
        }

        if ($event->solutions()->count() === 0) {
            $this->connection->update(Table::OPTIMIZATIONS,
                [
                    'progress' => $event->progress()->toJson(),
                    'state' => $event->state()->toInt(),
                    'updated_at' => $event->createdAt()->getTimestamp()
                ],
                ['model_id' => $event->modelId()->toString(), 'optimization_id' => $event->optimizationId()->toString()]
            );
        }
    }
}
