<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Model\Command\AddModflowScenario;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Model\ModflowModelList;

final class AddModflowScenarioHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * CreateModflowScenarioHandler constructor.
     * @param ModflowModelList $modelList
     */
    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(AddModflowScenario $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->baseModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->baseModelId());
        }

        if (! $modflowModel->ownerId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->ownerId());
        }

        $modflowModel->addScenario($command->userId(), $command->scenarioId());
    }
}
