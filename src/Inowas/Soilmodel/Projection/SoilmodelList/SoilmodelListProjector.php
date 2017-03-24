<?php

namespace Inowas\Soilmodel\Projection\SoilmodelList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Soilmodel\Model\Event\SoilmodelWasCreated;
use Inowas\Soilmodel\Projection\Table;

class SoilmodelListProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection)
    {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::SOILMODEL_LIST);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
    }

    public function onSoilmodelWasCreated(SoilmodelWasCreated $event): void
    {
        $this->connection->insert(Table::SOILMODEL_LIST, array(
            'user_id' => $event->userId()->toString(),
            'soilmodel_id' => $event->soilmodelId()->toString(),
            'name' => '',
            'description' => ''
        ));
    }
}
