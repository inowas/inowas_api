<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Optimization;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationMethodCollection;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Inowas\ModflowModel\Model\Event\OptimizationStateWasUpdated;
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
        $table->addColumn('methods', 'text', ['default' => '[]']);
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
            ['optimization_id' => $event->optimizationId()->toString()]
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
                ['input' => $event->input()->toJson(), 'updated_at' => $event->createdAt()->getTimestamp()],
                ['optimization_id' => $event->optimizationId()->toString()]
            );
        }
    }

    public function onOptimizationStateWasUpdated(OptimizationStateWasUpdated $event): void
    {
        if ($event->state()->toInt() === OptimizationState::NEW) {
            $this->clearResults($event->optimizationId());
        }

        $this->updateState($event->optimizationId(), $event->state(), $event->createdAt());

        if ($event->response() instanceof ModflowOptimizationResponse) {
            $this->updateResponse($event->modelId(), $event->optimizationId(), $event->response());
        }
    }

    protected function getMethods(ModflowId $optimizationId): OptimizationMethodCollection
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT methods FROM %s WHERE optimization_id = :optimization_id', Table::OPTIMIZATIONS),
            ['optimization_id' => $optimizationId->toString()]
        );

        if ($result === false) {
            return OptimizationMethodCollection::create();
        }

        return OptimizationMethodCollection::fromArray(json_decode($result['methods'], true));
    }

    protected function clearResults(ModflowId $optimizationId): void
    {
        $this->connection->update(Table::OPTIMIZATIONS,
            ['methods' => json_encode([])],
            ['optimization_id' => $optimizationId->toString()]
        );
    }

    protected function updateState(ModflowId $optimizationId, OptimizationState $state, \DateTimeImmutable $dateTime): void
    {
        $this->connection->update(Table::OPTIMIZATIONS,
            ['state' => $state->toInt(), 'updated_at' => $dateTime->getTimestamp()],
            ['optimization_id' => $optimizationId->toString()]
        );
    }

    protected function updateResponse(ModflowId $modelId, ModflowId $optimizationId, ModflowOptimizationResponse $response): void
    {
        /** @var OptimizationMethodCollection $methods */
        $persistedMethods = $this->getMethods($optimizationId);
        $persistedMethods->updateMethods($response->methods());
        $this->connection->update(Table::OPTIMIZATIONS,
            ['methods' => json_encode($persistedMethods->toArray())],
            ['optimization_id' => $optimizationId->toString(), 'model_id' => $modelId->toString()]
        );
    }
}
