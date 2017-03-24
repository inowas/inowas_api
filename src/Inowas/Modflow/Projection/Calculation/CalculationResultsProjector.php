<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\HeadWasCalculated;
use Inowas\Modflow\Projection\Table;

class CalculationResultsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_RESULTS);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('totim', 'integer');
        $table->addColumn('layer', 'integer');
        $table->addColumn('filename', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
    }

    public function onHeadWasCalculated(HeadWasCalculated $event): void
    {
        $this->connection->insert(Table::CALCULATION_RESULTS, array(
            'calculation_id' => $event->calculationId()->toString(),
            'type' => $event->type()->toString(),
            'totim' => $event->totalTime()->toInteger(),
            'layer' => $event->layer()->toInteger(),
            'filename' => $event->filename()->toString()
        ));
    }
}
