<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Model\Command\RemoveBoundary;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class RemoveBoundaryHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * @param ModflowModelList $modelList
     */
    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(RemoveBoundary $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->baseModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->baseModelId());
        }

        if (! $modflowModel->ownerId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->ownerId());
        }

        $modflowModel->removeBoundary($command->userId(), $command->boundaryId());
    }
}
