<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Infrastructure\Projection\BoundaryList\BoundaryFinder;
use Inowas\ModflowModel\Model\Command\UpdateActiveCells;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateActiveCellsHandler
{

    /** @var  BoundaryFinder */
    private $boundaryFinder;

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * @param ModflowModelList $modelList
     * @param BoundaryFinder $boundaryFinder
     */
    public function __construct(ModflowModelList $modelList, BoundaryFinder $boundaryFinder)
    {
        $this->boundaryFinder = $boundaryFinder;
        $this->modelList = $modelList;
    }

    public function __invoke(UpdateActiveCells $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        if ($command->isModelArea()) {
            $currentActiveCells = $this->boundaryFinder->findAreaActiveCells($command->modelId());
            if (! $currentActiveCells->sameAs($command->activeCells())){
                $modflowModel->updateAreaActiveCells($command->userId(), $command->activeCells());
            }
            return;
        }

        $currentActiveCells = $this->boundaryFinder->findBoundaryActiveCells($command->modelId(), $command->boundaryId());
        if (! $currentActiveCells->sameAs($command->activeCells())){
            $modflowModel->updateBoundaryActiveCells($command->userId(), $command->boundaryId(), $command->activeCells());
        }
    }
}
