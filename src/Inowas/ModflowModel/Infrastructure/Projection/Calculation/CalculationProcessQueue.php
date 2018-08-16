<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\CalculationProcessWasStarted;
use Inowas\ModflowModel\Model\Event\CalculationWasFinished;
use Inowas\ModflowModel\Model\Event\CalculationWasStarted;
use Inowas\ModflowModel\Model\Event\PreProcessingWasFinished;

class CalculationProcessQueue extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection)
    {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::CALCULATION_PROCESSES);
        $id = $table->addColumn('id', 'integer');
        $id->setAutoincrement(true);
        $table->addColumn('model_id', 'string', ['length' => 36, 'default' => '']);
        $table->addColumn('calculation_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('state', 'integer');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['model_id', 'calculation_id']);
        $this->addSchema($schema);
    }

    /**
     * @param CalculationProcessWasStarted $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onCalculationProcessWasStarted(CalculationProcessWasStarted $event): void
    {
        $this->connection->delete(Table::CALCULATION_PROCESSES, [
            'model_id' => $event->modelId()->toString(),
        ]);

        $this->connection->insert(Table::CALCULATION_PROCESSES, [
            'model_id' => $event->modelId()->toString(),
            'state' => $event->state()->toInt()
        ]);
    }

    public function onPreProcessingWasFinished(PreProcessingWasFinished $event): void
    {
        $this->connection->update(Table::CALCULATION_PROCESSES, [
            'calculation_id' => $event->calculationId()->toString(),
            'state' => $event->state()->toInt(),
        ], [
            'model_id' => $event->modelId()->toString()
        ]);
    }

    public function onCalculationWasStarted(CalculationWasStarted $event): void
    {
        $this->connection->update(Table::CALCULATION_PROCESSES,
            array('state' => CalculationState::calculating()->toInt()),
            array('model_id' => $event->modelId()->toString())
        );
    }

    /**
     * @param CalculationWasFinished $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onCalculationWasFinished(CalculationWasFinished $event): void
    {
        $this->connection->delete(Table::CALCULATION_PROCESSES, [
            'calculation_id' => $event->calculationId()->toString()
        ]);
    }
}
