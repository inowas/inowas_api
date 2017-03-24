<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\CalculationWasCreated;
use Inowas\Modflow\Projection\Table;

class CalculationListProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_LIST);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('grid_size', 'text');
        $table->addColumn('date_time_start', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('date_time_end', 'string', ['length' => 255, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    public function onCalculationWasCreated(CalculationWasCreated $event): void
    {
        $this->connection->insert(Table::CALCULATION_LIST, array(
            'calculation_id' => $event->calculationId()->toString(),
            'model_id' => $event->modflowModelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'soilmodel_id' => $event->soilModelId()->toString(),
            'grid_size' => json_encode($event->gridSize()),
            'date_time_start' => $event->startDateTime()->toAtom(),
            'date_time_end' => $event->endDateTime()->toAtom()
        ));
    }
}
