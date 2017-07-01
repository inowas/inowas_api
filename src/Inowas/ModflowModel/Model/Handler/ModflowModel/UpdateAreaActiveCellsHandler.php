<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler\ModflowModel;

use Inowas\ModflowModel\Model\Command\ModflowModel\UpdateAreaActiveCells;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\BoundaryManager;

final class UpdateAreaActiveCellsHandler
{

    /** @var  BoundaryManager */
    private $boundaryManager;

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * @param ModflowModelList $modelList
     * @param BoundaryManager $boundaryManager
     */
    public function __construct(ModflowModelList $modelList, BoundaryManager $boundaryManager)
    {
        $this->boundaryManager = $boundaryManager;
        $this->modelList = $modelList;
    }

    public function __invoke(UpdateAreaActiveCells $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $currentActiveCells = $this->boundaryManager->getAreaActiveCells($command->modelId());

        if (! $currentActiveCells->sameAs($command->activeCells())){
            $modflowModel->updateAreaActiveCells($command->userId(), $command->activeCells());
        }
    }
}
