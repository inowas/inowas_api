<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Optimization;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\CalculationIdWasChanged;
use Inowas\ModflowModel\Model\Event\OptimizationCalculationWasCanceled;
use Inowas\ModflowModel\Model\Event\OptimizationCalculationWasStarted;
use Inowas\ModflowModel\Model\Event\OptimizationProgressWasUpdated;

class OptimizationProcessQueue extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection)
    {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::OPTIMIZATION_PROCESSES);
        $id = $table->addColumn('id', 'integer');
        $id->setAutoincrement(true);
        $table->addColumn('model_id', 'string', ['length' => 36, 'default' => '']);
        $table->addColumn('calculation_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('optimization_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('state', 'integer');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['model_id', 'optimization_id']);
        $this->addSchema($schema);
    }

    /**
     * @param OptimizationCalculationWasStarted $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onOptimizationCalculationWasStarted(OptimizationCalculationWasStarted $event): void
    {
        $this->connection->delete(Table::OPTIMIZATION_PROCESSES, [
            'model_id' => $event->modelId()->toString(),
            'optimization_id' => $event->optimizationId()->toString()
        ]);

        $this->connection->insert(Table::OPTIMIZATION_PROCESSES, [
            'model_id' => $event->modelId()->toString(),
            'optimization_id' => $event->optimizationId()->toString(),
            'state' => $event->state()->toInt()
        ]);
    }

    public function onCalculationIdWasChanged(CalculationIdWasChanged $event): void
    {
        $this->connection->update(Table::OPTIMIZATION_PROCESSES, [
            'calculation_id' => $event->calculationId()->toString(),
            'state' => OptimizationState::calculating(),
        ], [
            'model_id' => $event->modelId()->toString()
        ]);
    }

    /**
     * @param OptimizationProgressWasUpdated $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onOptimizationProgressWasUpdated(OptimizationProgressWasUpdated $event): void
    {
        $this->connection->update(Table::OPTIMIZATION_PROCESSES,
            ['state' => CalculationState::calculating()->toInt()],
            ['optimization_id' => $event->optimizationId()->toString()]
        );

        if ($event->state()->toInt() === OptimizationState::finished()->toInt()) {
            $this->connection->delete(Table::OPTIMIZATION_PROCESSES, [
                'optimization_id' => $event->optimizationId()->toString()
            ]);
        }
    }

    /**
     * @param OptimizationCalculationWasCanceled $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onOptimizationCalculationWasCanceled(OptimizationCalculationWasCanceled $event): void
    {
        $this->connection->delete(Table::OPTIMIZATION_PROCESSES, [
            'model_id' => $event->modelId()->toString(),
            'optimization_id' => $event->optimizationId()->toString()
        ]);
    }
}
