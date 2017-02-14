<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\Modflow\Model\Exception\ModflowModelNotFoundException;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModel;

final class ChangeModflowModelBoundingBoxHandler
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

    public function __invoke(ChangeModflowModelBoundingBox $command)
    {
        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if ($command->userId()->sameValueAs($modflowModel->ownerId()))
        {
            $modflowModel->changeBoundingBox($command->userId(), $command->boundingBox());
        }
    }
}
