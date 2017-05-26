<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowCalculation\Infrastructure\Projection\Table;
use Inowas\ModflowCalculation\Model\Event\CalculationWasCreated;
use Inowas\ModflowCalculation\Model\Event\CalculationWasFinished;
use Inowas\ModflowCalculation\Model\Event\CalculationWasQueued;
use Inowas\ModflowCalculation\Model\Event\CalculationWasStarted;

class CalculationListProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_LIST);
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('start_date', 'string');
        $table->addColumn('end_date', 'string');
        $table->addColumn('state', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['calculation_id']);
    }

    public function onCalculationWasCreated(CalculationWasCreated $event): void
    {
        $this->connection->insert(Table::CALCULATION_LIST, array(
            'calculation_id' => $event->calculationId()->toString(),
            'model_id' => $event->modflowModelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'soilmodel_id' => $event->soilModelId()->toString(),
            'start_date' => $event->start()->toAtom(),
            'end_date' => $event->end()->toAtom()
        ));
    }

    public function onCalculationWasQueued(CalculationWasQueued $event): void
    {
        $this->connection->update(Table::CALCULATION_LIST, array(
            'state' => CalculationState::QUEUED
        ), array(
            'calculation_id' => $event->calculationId()->toString()
        ));
    }

    public function onCalculationWasStarted(CalculationWasStarted $event): void
    {
        $this->connection->update(Table::CALCULATION_LIST, array(
            'state' => CalculationState::STARTED
        ), array(
            'calculation_id' => $event->calculationId()->toString()
        ));
    }

    public function onCalculationWasFinished(CalculationWasFinished $event): void
    {
        $this->connection->update(Table::CALCULATION_LIST, array(
            'state' => CalculationState::FINISHED
        ), array(
            'calculation_id' => $event->calculationId()->toString()
        ));
    }
}
