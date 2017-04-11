<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\CalculationWasFinished;
use Inowas\Modflow\Projection\Table;

class CalculationResultsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_RESULTS);
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('heads', 'text');
        $table->addColumn('budgets', 'text');
        $table->addColumn('drawdowns', 'text');
        $table->addColumn('number_of_layers', 'integer', ['default' => 0]);
        $table->setPrimaryKey(['calculation_id']);
    }

    public function onCalculationWasFinished(CalculationWasFinished $event): void
    {
        $calculationId = $event->calculationId();
        $response = $event->response();

        if (! $this->calculationExists($event->calculationId())){
            $this->connection->insert(Table::CALCULATION_RESULTS, array(
                'calculation_id' => $calculationId->toString(),
                'heads' => json_encode($response->heads()),
                'budgets' => json_encode($response->budgets()),
                'drawdowns' => json_encode($response->drawdowns()),
                'number_of_layers' => $response->numberOfLayers()
            ));
            return;
        }

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

        if ($result['count'] === 1){
            return true;
        }

        return false;
    }
}
