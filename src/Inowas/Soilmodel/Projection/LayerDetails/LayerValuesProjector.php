<?php

namespace Inowas\Soilmodel\Projection\LayerDetails;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Common\Soilmodel\BottomElevation;
use Inowas\Common\Soilmodel\HydraulicAnisotropy;
use Inowas\Common\Soilmodel\HydraulicConductivityX;
use Inowas\Common\Soilmodel\SpecificStorage;
use Inowas\Common\Soilmodel\SpecificYield;
use Inowas\Common\Soilmodel\TopElevation;
use Inowas\Common\Soilmodel\VerticalHydraulicConductivity;
use Inowas\Soilmodel\Model\Event\LayerPropertyWasUpdated;
use Inowas\Soilmodel\Model\Event\LayerValuesWereUpdated;
use Inowas\Soilmodel\Model\Event\SoilmodelGeologicalLayerWasAdded;
use Inowas\Soilmodel\Projection\Table;

class LayerValuesProjector extends AbstractDoctrineConnectionProjector
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::LAYER_INTERPOLATIONS);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('layer_id', 'string', ['length' => 36]);
        $table->addColumn('layer_number', 'integer');
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('laytyp', 'integer', ['notnull' => false]);
        $table->addColumn('layavg', 'integer', ['notnull' => false]);
        $table->addColumn('chani', 'float', ['notnull' => false]);
        $table->addColumn('layvka', 'integer', ['notnull' => false]);
        $table->addColumn('laywet', 'integer', ['notnull' => false]);
        $table->addColumn('top', 'text', ['notnull' => false]);
        $table->addColumn('botm', 'text', ['notnull' => false]);
        $table->addColumn('hk', 'text', ['notnull' => false]);
        $table->addColumn('hani', 'text', ['notnull' => false]);
        $table->addColumn('vka', 'text', ['notnull' => false]);
        $table->addColumn('ss', 'text', ['notnull' => false]);
        $table->addColumn('sy', 'text', ['notnull' => false]);
    }


    public function onSoilmodelGeologicalLayerWasAdded(SoilmodelGeologicalLayerWasAdded $event): void
    {
        $this->connection->insert(Table::LAYER_INTERPOLATIONS, array(
            'soilmodel_id' => $event->soilmodelId()->toString(),
            'layer_id' => $event->layer()->id()->toString(),
            'layer_number' => $event->layer()->layerNumber()->toInteger(),
            'laytyp' => $event->layer()->layerType()->toInt(),
            'name' => $event->layer()->layerName()->toString(),
            'description' => $event->layer()->layerDescription()->toString()
        ));
    }

    public function onLayerPropertyWasUpdated(LayerPropertyWasUpdated $event): void
    {
        $property = $event->property();
        if ($property instanceof BottomElevation) {
            $this->connection->update(Table::LAYER_INTERPOLATIONS, array(
                'botm' => json_encode($property->toValue())
            ), array(
                    'soilmodel_id' => $event->soilmodelId()->toString(),
                    'layer_id' => $event->layerId()->toString())
            );
        }
        if ($property instanceof HydraulicAnisotropy) {
            $this->connection->update(Table::LAYER_INTERPOLATIONS, array(
                'hani' => json_encode($property->toValue())
            ), array(
                    'soilmodel_id' => $event->soilmodelId()->toString(),
                    'layer_id' => $event->layerId()->toString())
            );
        }
        if ($property instanceof HydraulicConductivityX) {
            $this->connection->update(Table::LAYER_INTERPOLATIONS, array(
                'hk' => json_encode($property->toValue())
            ), array(
                    'soilmodel_id' => $event->soilmodelId()->toString(),
                    'layer_id' => $event->layerId()->toString())
            );
        }
        if ($property instanceof SpecificStorage) {
            $this->connection->update(Table::LAYER_INTERPOLATIONS, array(
                'ss' => json_encode($property->toValue())
            ), array(
                    'soilmodel_id' => $event->soilmodelId()->toString(),
                    'layer_id' => $event->layerId()->toString())
            );
        }
        if ($property instanceof SpecificYield) {
            $this->connection->update(Table::LAYER_INTERPOLATIONS, array(
                'sy' => json_encode($property->toValue())
            ), array(
                    'soilmodel_id' => $event->soilmodelId()->toString(),
                    'layer_id' => $event->layerId()->toString())
            );
        }
        if ($property instanceof TopElevation) {
            $this->connection->update(Table::LAYER_INTERPOLATIONS, array(
                'top' => json_encode($property->toValue())
            ), array(
                    'soilmodel_id' => $event->soilmodelId()->toString(),
                    'layer_id' => $event->layerId()->toString())
            );
        }
        if ($property instanceof VerticalHydraulicConductivity) {
            $this->connection->update(Table::LAYER_INTERPOLATIONS, array(
                'vka' => json_encode($property->toValue())
            ), array(
                    'soilmodel_id' => $event->soilmodelId()->toString(),
                    'layer_id' => $event->layerId()->toString())
            );
        }
    }

    public function onLayerValuesWereUpdated(LayerValuesWereUpdated $event): void
    {
        $this->connection->update(Table::LAYER_INTERPOLATIONS, array(
            'layavg' => $event->values()->layavg()->toInt(),
            'chani' => $event->values()->conductivity()->chani()->toValue(),
            'layvka' => $event->values()->conductivity()->layVka()->toValue(),
            'laywet' => $event->values()->laywet()->toValue(),
            'hk' => $event->values()->conductivity()->hk()->toValue(),
            'hani' => $event->values()->conductivity()->hani()->toValue(),
            'vka' => $event->values()->conductivity()->vka()->toValue(),
            'ss' => $event->values()->storage()->ss()->toValue(),
            'sy' => $event->values()->storage()->sy()->toValue()
        ), array(
            'soilmodel_id' => $event->soilmodelId()->toString(),
            'layer_id' => $event->layerId()->toString())
        );
    }
}
