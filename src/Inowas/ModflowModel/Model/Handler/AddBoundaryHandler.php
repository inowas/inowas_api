<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class AddBoundaryHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    /**
     * AddBoundaryHandler constructor.
     * @param ModflowModelList $modelList
     * @param GeoTools $geoTools
     */
    public function __construct(ModflowModelList $modelList, GeoTools $geoTools)
    {
        $this->geoTools = $geoTools;
        $this->modelList = $modelList;
    }

    public function __invoke(AddBoundary $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $modflowModel->addBoundary($command->userId(), $command->boundary());
    }
}
