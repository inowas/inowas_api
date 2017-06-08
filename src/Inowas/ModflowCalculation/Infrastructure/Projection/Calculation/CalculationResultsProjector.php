<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowCalculation\Infrastructure\Projection\Table;
use Inowas\ModflowCalculation\Model\Event\CalculationWasCloned;
use Inowas\ModflowCalculation\Model\Event\CalculationWasCreated;
use Inowas\ModflowCalculation\Model\Event\CalculationWasFinished;

class CalculationResultsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_RESULTS);
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('start_date_time', 'string');
        $table->addColumn('time_unit', 'integer');
        $table->addColumn('heads', 'text', ['default' => '[]']);
        $table->addColumn('budgets', 'text', ['default' => '[]']);
        $table->addColumn('drawdowns', 'text', ['default' => '[]']);
        $table->addColumn('number_of_layers', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['calculation_id']);
    }

    public function onCalculationWasCreated(CalculationWasCreated $event): void
    {
        $this->connection->insert(Table::CALCULATION_RESULTS, array(
            'calculation_id' => $event->calculationId()->toString(),
            'start_date_time' => $event->start()->toAtom(),
            'time_unit' => $event->timeUnit()->toInt()
        ));
    }

    public function onCalculationWasCloned(CalculationWasCloned $event): void
    {
        $this->connection->insert(Table::CALCULATION_RESULTS, array(
            'calculation_id' => $event->calculationId()->toString(),
            'start_date_time' => $event->start()->toAtom(),
            'time_unit' => $event->timeUnit()->toInt()
        ));
    }

    public function onCalculationWasFinished(CalculationWasFinished $event): void
    {
        $calculationId = $event->calculationId();
        $response = $event->response();

        $this->connection->update(Table::CALCULATION_RESULTS,
            array(
                'heads' => json_encode($response->heads()),
                'budgets' => json_encode($response->budgets()),
                'drawdowns' => json_encode($response->drawdowns()),
                'number_of_layers' => $response->numberOfLayers()
            ), array(
                'calculation_id' => $calculationId->toString()
            )
        );
    }

    protected function calculationExists(ModflowId $calculationId): bool
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE calculation_id = :calculation_id', Table::CALCULATION_RESULTS),
            ['calculation_id' => $calculationId->toString()]
        );

        return $result['count'] === 1;
    }
}
