<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Soilmodel\GeologicalLayer;
use Inowas\Common\Soilmodel\GeologicalLayerDescription;
use Inowas\Common\Soilmodel\GeologicalLayerId;
use Inowas\Common\Soilmodel\GeologicalLayerName;
use Inowas\Common\Soilmodel\GeologicalLayerNumber;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\Soilmodel\Model\Command\AddGeologicalLayerToSoilmodel;
use Inowas\Soilmodel\Model\Command\CreateSoilmodel;
use Prooph\ServiceBus\CommandBus;

final class CreateModflowModelHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    /** @var  CommandBus */
    private $commandBus;

    public function __construct(ModflowModelList $modelList, GeoTools $geoTools, CommandBus $commandBus)
    {
        $this->modelList = $modelList;
        $this->geoTools = $geoTools;
        $this->commandBus = $commandBus;
    }

    public function __invoke(CreateModflowModel $command)
    {
        $boundingBox = $this->geoTools->getBoundingBoxFromPolygon($command->area()->geometry());

        $soilmodelId = SoilmodelId::generate();
        $this->commandBus->dispatch(CreateSoilmodel::byUserWithModelId(
            $command->userId(),
            $soilmodelId
        ));

        $geologicalLayer = GeologicalLayer::fromParams(
            GeologicalLayerId::generate(),
            Laytyp::fromInt(Laytyp::TYPE_CONFINED),
            GeologicalLayerNumber::fromInteger(0),
            GeologicalLayerName::fromString('Layer 1'),
            GeologicalLayerDescription::fromString('Layer 1')
        );

        $this->commandBus->dispatch(AddGeologicalLayerToSoilmodel::forSoilmodel($command->userId(), $soilmodelId, $geologicalLayer));

        $calculationId = $command->calculationId();
        if (null === $calculationId){
            $calculationId = ModflowId::generate();
        }

        $modflowModel = ModflowModelAggregate::create(
            $command->modflowModelId(),
            $command->userId(),
            $command->name(),
            $command->description(),
            $soilmodelId,
            $command->area(),
            $command->gridSize(),
            $boundingBox,
            $command->lengthUnit(),
            $command->timeUnit(),
            $calculationId
        );

        $this->modelList->add($modflowModel);
    }
}
