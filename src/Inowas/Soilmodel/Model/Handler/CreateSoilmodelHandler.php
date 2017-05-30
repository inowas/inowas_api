<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Handler;

use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Soilmodel\GeologicalLayer;
use Inowas\Common\Soilmodel\GeologicalLayerDescription;
use Inowas\Common\Soilmodel\GeologicalLayerId;
use Inowas\Common\Soilmodel\GeologicalLayerName;
use Inowas\Common\Soilmodel\GeologicalLayerNumber;
use Inowas\Soilmodel\Model\Command\CreateSoilmodel;
use Inowas\Soilmodel\Model\SoilmodelAggregate;
use Inowas\Soilmodel\Model\SoilmodelList;

final class CreateSoilmodelHandler
{

    /** @var  SoilmodelList */
    private $soilmodelList;

    public function __construct(SoilmodelList $soilmodelList)
    {
        $this->soilmodelList = $soilmodelList;
    }

    public function __invoke(CreateSoilmodel $command)
    {
        $soilmodel = SoilmodelAggregate::create(
            $command->userId(),
            $command->soilmodelId()
        );

        # Creating a soilmodel adds a default-layer
        $soilmodel->addGeologicalLayer(
            $command->userId(),
            GeologicalLayer::fromParams(
                GeologicalLayerId::generate(),
                Laytyp::fromInt(Laytyp::TYPE_CONFINED),
                GeologicalLayerNumber::fromInteger(0),
                GeologicalLayerName::fromString('Layer 1'),
                GeologicalLayerDescription::fromString('Layer 1'),
                null
            )
        );

        $this->soilmodelList->add($soilmodel);
    }
}
