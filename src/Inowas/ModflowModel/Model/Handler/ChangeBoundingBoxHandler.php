<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Geometry\Srid;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ModflowModel\Model\Command\ChangeBoundingBox;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class ChangeBoundingBoxHandler
{

    /** @var  ModelFinder */
    private $modelFinder;

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    /**
     * ChangeModflowModelBoundingBoxHandler constructor.
     * @param ModflowModelList $modelList
     * @param GeoTools $geoTools
     * @param ModelFinder $modelFinder
     */
    public function __construct(ModflowModelList $modelList, GeoTools $geoTools, ModelFinder $modelFinder)
    {
        $this->modelFinder = $modelFinder;
        $this->modelList = $modelList;
        $this->geoTools = $geoTools;
    }

    public function __invoke(ChangeBoundingBox $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $currentBoundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($command->modflowModelId());

        if (! $command->boundingBox()->sameAs($currentBoundingBox)){
            $boundingBox = $this->geoTools->projectBoundingBox($command->boundingBox(), Srid::fromInt(4326));
            $modflowModel->changeBoundingBox($command->userId(), $boundingBox);
        }
    }
}
