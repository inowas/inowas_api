<?php

namespace Inowas\Modflow\Projection\PackageData;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Soilmodel\Model\Event\LayerValuesWereUpdated;
use Inowas\Soilmodel\Projection\Table;

class MfPackageProjector extends AbstractDoctrineConnectionProjector
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::MF_PACKAGE);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('model_name', 'string', ['length' => 36]);
        $table->addColumn('exe_name', 'string', ['length' => 36]);
        $table->addColumn('model_ws', 'string', ['length' => 36]);
        $table->addColumn('verbose', 'boolean');
        $table->setPrimaryKey(['id']);
    }

    public function onLayerValuesWereUpdated(LayerValuesWereUpdated $event): void
    {
        $layerValues = $event->values();
        $values = array(
            $layerValues->hTop()->identifier() => $layerValues->hTop()->toArray(),
            $layerValues->hBottom()->identifier() => $layerValues->hBottom()->toArray(),
            $layerValues->conductivity()->kx()->identifier() => $layerValues->conductivity()->kx()->toArray(),
            $layerValues->conductivity()->ky()->identifier() => $layerValues->conductivity()->ky()->toArray(),
            $layerValues->conductivity()->kz()->identifier() => $layerValues->conductivity()->kz()->toArray(),
            $layerValues->conductivity()->ha()->identifier() => $layerValues->conductivity()->ha()->toArray(),
            $layerValues->storage()->ss()->identifier() => $layerValues->storage()->ss()->toArray(),
            $layerValues->storage()->sy()->identifier() => $layerValues->storage()->sy()->toArray()
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
