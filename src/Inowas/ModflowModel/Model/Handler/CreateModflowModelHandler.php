<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class CreateModflowModelHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    public function __construct(ModflowModelList $modelList, GeoTools $geoTools)
    {
        $this->geoTools = $geoTools;
        $this->modelList = $modelList;
    }

    public function __invoke(CreateModflowModel $command)
    {
        /**
         * @TODO Retrieve Units from UserProfile
         */
        $lengthUnit = LengthUnit::fromInt(LengthUnit::METERS);
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);

        $boundingBox = $this->geoTools->getBoundingBoxFromPolygon($command->area()->geometry());

        $modflowModel = ModflowModelAggregate::create(
            $command->modflowModelId(),
            $command->userId(),
            $command->area(),
            $command->gridSize(),
            $boundingBox,
            $lengthUnit,
            $timeUnit
        );

        $this->modelList->add($modflowModel);
    }
}
