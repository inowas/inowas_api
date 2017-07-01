<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\ModflowModel\CalculationWasFinished;
use Inowas\ModflowModel\Model\Event\ModflowModel\CalculationWasStarted;

class CalculationResultsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::CALCULATIONS);
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('state', 'integer', ['default' => 0]);
        $table->addColumn('message', 'text', ['notnull' => false]);
        $table->addColumn('heads', 'text', ['default' => '[]']);
        $table->addColumn('budgets', 'text', ['default' => '[]']);
        $table->addColumn('drawdowns', 'text', ['default' => '[]']);
        $table->addColumn('number_of_layers', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['calculation_id']);
        $this->addSchema($schema);
    }

    public function onCalculationWasStarted(CalculationWasStarted $event): void
    {
        $this->connection->insert(Table::CALCULATIONS, array(
            'calculation_id' => $event->calculationId()->toString(),
            'state' => 2
        ));
    }

    public function onCalculationWasFinished(CalculationWasFinished $event): void
    {
        $response = $event->response();

        $this->connection->update(Table::CALCULATIONS,
            array(
                'state' => 3,
                'message' => $response->message(),
                'heads' => json_encode($response->heads()),
                'budgets' => json_encode($response->budgets()),
                'drawdowns' => json_encode($response->drawdowns()),
                'number_of_layers' => $response->numberOfLayers()
            ), array(
                'calculation_id' => $event->calculationId()->toString()
            )
        );
    }
}
