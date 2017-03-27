<?php

namespace Inowas\Soilmodel\Projection\LayerDetails;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Soilmodel\Model\Event\SoilmodelGeologicalLayerWasAdded;
use Inowas\Soilmodel\Projection\Table;

class LayerDetailsProjector extends AbstractDoctrineConnectionProjector
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::LAYER_DETAILS);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('layer_id', 'string', ['length' => 36]);
        $table->addColumn('layer_type', 'integer');
        $table->addColumn('layer_number', 'integer');
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
    }

    public function onSoilmodelGeologicalLayerWasAdded(SoilmodelGeologicalLayerWasAdded $event): void
    {
        $this->connection->insert(Table::LAYER_DETAILS, array(
            'soilmodel_id' => $event->soilmodelId()->toString(),
            'layer_id' => $event->layer()->id()->toString(),
            'layer_type' => $event->layer()->layerType()->toInt(),
            'layer_number' => $event->layer()->layerNumber()->toInteger(),
            'name' => $event->layer()->layerName()->toString(),
            'description' => $event->layer()->layerDescription()->toString()
        ));
    }
}
