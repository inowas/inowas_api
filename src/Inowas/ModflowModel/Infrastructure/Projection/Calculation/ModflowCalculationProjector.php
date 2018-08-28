<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\AMQP\ModflowCalculationResponse;
use Inowas\ModflowModel\Model\Event\CalculationStateWasUpdated;

class ModflowCalculationProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::MODELS_CALCULATIONS);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('calculation_id', 'string', ['length' => 36, 'default' => null, 'notnull' => false]);
        $table->addColumn('state', 'integer', ['notnull' => false]);
        $table->addColumn('created_at', 'integer', ['default' => 0]);
        $table->addColumn('updated_at', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['model_id']);
        $this->addSchema($schema);

        $schema = new Schema();
        $table = $schema->createTable(Table::CALCULATIONS);
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('state', 'integer', ['default' => 0]);
        $table->addColumn('message', 'text', ['default' => '']);
        $table->addColumn('heads', 'text', ['default' => '[]']);
        $table->addColumn('budgets', 'text', ['default' => '[]']);
        $table->addColumn('drawdowns', 'text', ['default' => '[]']);
        $table->addColumn('number_of_layers', 'integer', ['default' => 0]);
        $table->addColumn('created_at', 'integer', ['default' => 0]);
        $table->addColumn('updated_at', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['calculation_id']);
        $this->addSchema($schema);
    }

    /**
     * @param CalculationStateWasUpdated $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onCalculationStateWasUpdated(CalculationStateWasUpdated $event): void
    {
        $state = $event->state()->toInt();

        switch ($state) {
            case CalculationState::calculationProcessStarted()->toInt():
                $this->createModelCalculation($event);
                break;
            case CalculationState::PREPROCESSING:
                $this->updateModelCalculationState($event);
                break;
            case CalculationState::PREPROCESSING_FINISHED:
                $this->updateModelCalculationState($event);
                $this->createCalculationIfNotExists($event);
                break;
            case CalculationState::QUEUED:
                $this->updateModelCalculationState($event);
                $this->updateCalculationState($event);
                break;
            case CalculationState::CALCULATING:
                $this->updateModelCalculationState($event);
                $this->updateCalculationState($event);
                break;
            case CalculationState::CALCULATION_FINISHED:
                $this->updateModelCalculationState($event);
                $this->updateCalculationState($event);
                $this->updateCalculationResults($event);
                break;
            default:
                return;
        }
    }

    /**
     * @param CalculationStateWasUpdated $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    private function createModelCalculation(CalculationStateWasUpdated $event): void
    {
        if (!$event->modelId() instanceof ModflowId) {
            return;
        }

        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) from %s WHERE model_id = :model_id', Table::MODELS_CALCULATIONS),
            ['model_id' => $event->modelId()->toString()]
        );

        if ($result['count'] > 0) {
            $this->connection->delete(Table::MODELS_CALCULATIONS, [
                'model_id' => $event->modelId()->toString()
            ]);
        }

        $this->connection->insert(Table::MODELS_CALCULATIONS, [
            'model_id' => $event->modelId()->toString(),
            'state' => $event->state()->toInt(),
            'created_at' => $event->createdAt()->getTimestamp(),
            'updated_at' => $event->createdAt()->getTimestamp()
        ]);
    }

    private function updateModelCalculationState(CalculationStateWasUpdated $event): void
    {
        if ($event->modelId() instanceof ModflowId && $event->calculationId() === null) {
            $this->connection->update(Table::MODELS_CALCULATIONS,
                [
                    'state' => $event->state()->toInt(),
                    'created_at' => $event->createdAt()->getTimestamp(),
                    'updated_at' => $event->createdAt()->getTimestamp()
                ],
                ['model_id' => $event->modelId()->toString()]
            );
        }

        if ($event->modelId() instanceof ModflowId && $event->calculationId() instanceof CalculationId) {
            $this->connection->update(Table::MODELS_CALCULATIONS,
                [
                    'calculation_id' => $event->calculationId()->toString(),
                    'state' => $event->state()->toInt(),
                    'created_at' => $event->createdAt()->getTimestamp(),
                    'updated_at' => $event->createdAt()->getTimestamp()
                ],
                ['model_id' => $event->modelId()->toString()]
            );
        }

        if ($event->modelId() === null && $event->calculationId() instanceof CalculationId) {
            $this->connection->update(Table::MODELS_CALCULATIONS,
                [
                    'state' => $event->state()->toInt(),
                    'created_at' => $event->createdAt()->getTimestamp(),
                    'updated_at' => $event->createdAt()->getTimestamp()
                ],
                ['calculation_id' => $event->calculationId()->toString()]
            );
        }
    }

    private function createCalculationIfNotExists(CalculationStateWasUpdated $event): void
    {
        if (!$event->calculationId() instanceof CalculationId) {
            return;
        }

        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) from %s WHERE calculation_id = :calculation_id', Table::CALCULATIONS),
            ['calculation_id' => $event->calculationId()->toString()]
        );

        if ($result['count'] > 0) {
            return;
        }

        $this->connection->insert(Table::CALCULATIONS, [
            'calculation_id' => $event->calculationId()->toString(),
            'state' => $event->state()->toInt(),
            'created_at' => $event->createdAt()->getTimestamp(),
            'updated_at' => $event->createdAt()->getTimestamp()
        ]);
    }

    private function updateCalculationState(CalculationStateWasUpdated $event): void
    {
        if (!$event->calculationId() instanceof CalculationId) {
            return;
        }

        $this->connection->update(Table::CALCULATIONS, [
            'state' => $event->state()->toInt(),
            'created_at' => $event->createdAt()->getTimestamp(),
            'updated_at' => $event->createdAt()->getTimestamp()
        ],
            ['calculation_id' => $event->calculationId()->toString()]
        );
    }

    private function updateCalculationResults(CalculationStateWasUpdated $event): void
    {
        if (!$event->response() instanceof ModflowCalculationResponse) {
            return;
        }

        if (!$event->calculationId() instanceof CalculationId) {
            return;
        }

        $this->connection->update(Table::CALCULATIONS, [
            'state' => $event->state()->toInt(),
            'message' => $event->response()->message(),
            'heads' => \json_encode($event->response()->heads()),
            'budgets' => \json_encode($event->response()->budgets()),
            'drawdowns' => \json_encode($event->response()->drawdowns()),
            'number_of_layers' => $event->response()->numberOfLayers(),
            'created_at' => $event->createdAt()->getTimestamp(),
            'updated_at' => $event->createdAt()->getTimestamp()
        ],
            ['calculation_id' => $event->calculationId()->toString()]
        );

    }
}
