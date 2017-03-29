<?php

namespace Inowas\Soilmodel\Projection\LayerDetails;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Soilmodel\Model\Event\LayerValuesWereUpdated;
use Inowas\Soilmodel\Projection\Table;

class LayerValuesProjector extends AbstractDoctrineConnectionProjector
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::LAYER_INTERPOLATIONS);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('layer_id', 'string', ['length' => 36]);
        $table->addColumn('layer_number', 'integer');
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('values', 'text');
        $table->setPrimaryKey(['id']);
    }

    public function onLayerValuesWereUpdated(LayerValuesWereUpdated $event): void
    {
        $layerValues = $event->values();
        $values = array(
            $layerValues->hTop()->identifier() => $layerValues->hTop()->toValue(),
            $layerValues->hBottom()->identifier() => $layerValues->hBottom()->toValue(),
            $layerValues->conductivity()->kx()->identifier() => $layerValues->conductivity()->kx()->toValue(),
            $layerValues->conductivity()->ky()->identifier() => $layerValues->conductivity()->ky()->toValue(),
            $layerValues->conductivity()->kz()->identifier() => $layerValues->conductivity()->kz()->toValue(),
            $layerValues->conductivity()->ha()->identifier() => $layerValues->conductivity()->ha()->toValue(),
            $layerValues->storage()->ss()->identifier() => $layerValues->storage()->ss()->toValue(),
            $layerValues->storage()->sy()->identifier() => $layerValues->storage()->sy()->toValue()
        );

        foreach ($values as $key => $value){
            $this->connection->insert(Table::LAYER_INTERPOLATIONS, array(
                'soilmodel_id' => $event->soilmodelId()->toString(),
                'layer_id' => $event->layerId()->toString(),
                'layer_number' => $event->layerNumber()->toInteger(),
                'type' => $key,
                'values' => json_encode($value)
            ));
        }
    }
}
