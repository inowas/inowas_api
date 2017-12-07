<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\ModflowModelManager;

final class AddBoundaryHandler
{

    /** @var  GeoTools */
    private $geoTools;

    /** @var  ModflowModelList */
    private $modelList;


    /** @var  ModflowModelManager */
    private $modelManager;

    /**
     * ChangeModflowModelBoundingBoxHandler constructor.
     * @param ModflowModelList $modelList
     * @param ModflowModelManager $manager
     * @param GeoTools $geoTools
     */
    public function __construct(ModflowModelList $modelList, ModflowModelManager $manager, GeoTools $geoTools) {
        $this->geoTools = $geoTools;
        $this->modelList = $modelList;
        $this->modelManager = $manager;
    }

    public function __invoke(AddBoundary $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $boundary = $command->boundary();
        if ($boundary->affectedCells()->isEmpty()) {
            $boundingBox = $this->modelManager->getBoundingBox($command->modflowModelId());
            $gridSize = $this->modelManager->getGridSize($command->modflowModelId());
            $affectedCells = $this->geoTools->calculateActiveCellsFromGeometryAndAffectedLayers($boundary->geometry(), $boundary->affectedLayers(), $boundingBox, $gridSize)->affectedCells();
            $boundary = $boundary->updateAffectedCells($affectedCells);
        }

        $modflowModel->addBoundary($command->userId(), $boundary);
        $this->modelList->save($modflowModel);
    }
}
