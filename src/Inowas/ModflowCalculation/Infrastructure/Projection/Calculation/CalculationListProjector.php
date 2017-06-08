<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowCalculation\Infrastructure\Projection\Table;
use Inowas\ModflowCalculation\Model\Event\CalculationWasCreated;
use Inowas\ModflowCalculation\Model\Event\CalculationWasFinished;
use Inowas\ModflowCalculation\Model\Event\CalculationWasQueued;
use Inowas\ModflowCalculation\Model\Event\CalculationWasStarted;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ModflowModel\Service\ModflowModelManager;

class CalculationListProjector extends AbstractDoctrineConnectionProjector
{

    /** @var  ModelFinder */
    protected $modelFinder;

    public function __construct(Connection $connection, ModelFinder $modelFinder) {

        $this->modelFinder = $modelFinder;

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_LIST);
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('start_date_time', 'string');
        $table->addColumn('end_date_time', 'string');
        $table->addColumn('calculation_state', 'integer', ['default' => 0]);
        $table->addColumn('created_at', 'string');
        $table->setPrimaryKey(['calculation_id']);
    }

    public function onCalculationWasCreated(CalculationWasCreated $event): void
    {
        $soilmodelId = $this->modelFinder->getSoilmodelIdByModelId($event->modflowmodelId());

        $this->connection->insert(Table::CALCULATION_LIST, array(
            'calculation_id' => $event->calculationId()->toString(),
            'model_id' => $event->modflowmodelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'soilmodel_id' => $soilmodelId->toString(),
            'start_date_time' => $event->start()->toAtom(),
            'end_date_time' => $event->end()->toAtom(),
            'created_at' => DateTime::fromDateTimeImmutable($event->createdAt())->toAtom()
        ));
    }

    public function onCalculationWasQueued(CalculationWasQueued $event): void
    {
        $this->connection->update(Table::CALCULATION_LIST, array(
            'calculation_state' => CalculationState::QUEUED
        ), array(
            'calculation_id' => $event->calculationId()->toString()
        ));
    }

    public function onCalculationWasStarted(CalculationWasStarted $event): void
    {
        $this->connection->update(Table::CALCULATION_LIST, array(
            'calculation_state' => CalculationState::STARTED
        ), array(
            'calculation_id' => $event->calculationId()->toString()
        ));
    }

    public function onCalculationWasFinished(CalculationWasFinished $event): void
    {
        $this->connection->update(Table::CALCULATION_LIST, array(
            'calculation_state' => CalculationState::FINISHED
        ), array(
            'calculation_id' => $event->calculationId()->toString()
        ));
    }
}
