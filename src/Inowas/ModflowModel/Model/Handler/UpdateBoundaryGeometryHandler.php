<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Infrastructure\Projection\BoundaryList\BoundaryFinder;
use Inowas\ModflowModel\Model\Command\UpdateBoundaryGeometry;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateBoundaryGeometryHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    /** @var  BoundaryFinder */
    private $boundaryFinder;

    /**
     * UpdateBoundaryGeometryHandler constructor.
     * @param ModflowModelList $modelList
     * @param GeoTools $geoTools
     * @param BoundaryFinder $boundaryFinder
     */
    public function __construct(ModflowModelList $modelList, GeoTools $geoTools, BoundaryFinder $boundaryFinder)
    {
        $this->modelList = $modelList;
        $this->geoTools = $geoTools;
        $this->boundaryFinder = $boundaryFinder;
    }

    public function __invoke(UpdateBoundaryGeometry $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $modflowModel->updateBoundaryGeometry($command->userId(), $command->boundaryId(), $command->geometry());
    }
}
