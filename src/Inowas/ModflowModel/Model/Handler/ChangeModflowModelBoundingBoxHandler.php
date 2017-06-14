<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Geometry\Srid;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class ChangeModflowModelBoundingBoxHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    /**
     * ChangeModflowModelBoundingBoxHandler constructor.
     * @param ModflowModelList $modelList
     * @param GeoTools $geoTools
     */
    public function __construct(ModflowModelList $modelList, GeoTools $geoTools)
    {
        $this->modelList = $modelList;
        $this->geoTools = $geoTools;
    }

    public function __invoke(ChangeModflowModelBoundingBox $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if (! $modflowModel->ownerId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->ownerId());
        }

        $boundingBox = $this->geoTools->projectBoundingBox($command->boundingBox(), Srid::fromInt(4326));
        $modflowModel->changeBoundingBox($command->userId(), $boundingBox);
    }
}
