<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Handler;

use Inowas\ModflowBoundary\Model\Command\UpdateBoundaryActiveCells;
use Inowas\ModflowBoundary\Model\Exception\ModflowBoundaryNotFoundException;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowBoundary\Model\ModflowBoundaryAggregate;
use Inowas\ModflowBoundary\Model\ModflowBoundaryList;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowBoundary\Service\BoundaryManager;

final class UpdateBoundaryActiveCellsHandler
{

    /** @var  BoundaryManager */
    private $boundaryManager;

    /** @var  ModflowBoundaryList */
    private $boundaryList;

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * @param ModflowModelList $modelList
     * @param ModflowBoundaryList $boundaryList
     * @param BoundaryManager $boundaryManager
     */
    public function __construct(ModflowModelList $modelList, ModflowBoundaryList $boundaryList, BoundaryManager $boundaryManager)
    {
        $this->boundaryList = $boundaryList;
        $this->boundaryManager = $boundaryManager;
        $this->modelList = $modelList;
    }

    public function __invoke(UpdateBoundaryActiveCells $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        /** @var \Inowas\ModflowBoundary\Model\ModflowBoundaryAggregate $boundary */
        $boundary = $this->boundaryList->get($command->boundaryId());

        if (! $boundary){
            throw ModflowBoundaryNotFoundException::withId($command->boundaryId());
        }

        $currentActiveCells = $this->boundaryManager->getBoundaryActiveCells($command->modelId(), $command->boundaryId());
        if (! $currentActiveCells->sameAs($command->activeCells())){
            $boundary->updateActiveCells($command->userId(), $command->activeCells());
        }
    }
}
