<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Common\Boundaries\AreaBoundary;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\GeoToolsBundle\Service\GeoTools;
use Inowas\Modflow\Model\Command\CalculateActiveCells;
use Inowas\Modflow\Model\Exception\ModflowModelNotFoundException;
use Inowas\Modflow\Model\Exception\WriteAccessFailedException;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelAggregate;

final class CalculateActiveCellsHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    /**
     * CalculateActiveCellsHandler constructor.
     * @param ModflowModelList $modelList
     * @param GeoTools $geoTools
     */
    public function __construct(ModflowModelList $modelList, GeoTools $geoTools)
    {
        $this->modelList = $modelList;
        $this->geoTools = $geoTools;
    }

    public function __invoke(CalculateActiveCells $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $modflowModel->ownerId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->ownerId());
        }


        $boundary = $modflowModel->findBoundaryById($command->boundaryId());
        if (is_null($boundary)){
            return;
        }

        if (!$modflowModel->boundingBox() instanceof BoundingBox){
            return;
        }

        if (!$modflowModel->gridSize() instanceof GridSize){
            return;
        }

        if ($boundary instanceof AreaBoundary){
            $activeCells = $this->geoTools->getActiveCellsFromArea($modflowModel->area(), $modflowModel->boundingBox(), $modflowModel->gridSize());

            if ($activeCells instanceof ActiveCells){
                $modflowModel->updateActiveCells($command->userId(), $command->boundaryId(), $boundary->type(), $activeCells);
            }
        }
    }
}
