<?php

namespace Inowas\ModflowModel\Infrastructure\ProcessManager;

use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Soilmodel\GeologicalLayer;
use Inowas\Common\Soilmodel\GeologicalLayerDescription;
use Inowas\Common\Soilmodel\GeologicalLayerId;
use Inowas\Common\Soilmodel\GeologicalLayerName;
use Inowas\Common\Soilmodel\GeologicalLayerNumber;
use Inowas\ModflowModel\Model\Event\ModflowModel\ModflowModelWasCreated;
use Inowas\Soilmodel\Model\Command\AddGeologicalLayerToSoilmodel;
use Inowas\Soilmodel\Model\Command\CreateSoilmodel;
use Inowas\Soilmodel\Model\SoilmodelAggregate;
use Inowas\Soilmodel\Model\SoilmodelList;
use Prooph\ServiceBus\CommandBus;

class CreateModflowModelProcessManager
{

    /** @var  CommandBus */
    private $commandBus;

    /** @var  SoilmodelList */
    private $soilmodelList;

    public function __construct(CommandBus $commandBus, SoilmodelList $soilmodelList)
    {
        $this->commandBus = $commandBus;
        $this->soilmodelList = $soilmodelList;
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $soilmodelId = $event->soilmodelId();
        $soilmodel = $this->soilmodelList->get($event->soilmodelId());

        if (! $soilmodel instanceof SoilmodelAggregate){
            $this->commandBus->dispatch(CreateSoilmodel::byUserWithModelId(
                $event->userId(),
                $soilmodelId
            ));

            $geologicalLayer = GeologicalLayer::fromParams(
                GeologicalLayerId::generate(),
                Laytyp::fromInt(Laytyp::TYPE_CONFINED),
                GeologicalLayerNumber::fromInteger(0),
                GeologicalLayerName::fromString('Layer 1'),
                GeologicalLayerDescription::fromString('Layer 1')
            );

            $this->commandBus->dispatch(AddGeologicalLayerToSoilmodel::forSoilmodel($event->userId(), $soilmodelId, $geologicalLayer));
        }
    }
}
