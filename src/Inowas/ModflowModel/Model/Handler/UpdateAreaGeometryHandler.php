<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Id\BoundaryId;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\UpdateAreaGeometry;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateAreaGeometryHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var GeoTools */
    private $geoTools;

    /**
     * UpdateAreaGeometryHandler constructor.
     * @param ModflowModelList $modelList
     * @param GeoTools $geoTools
     */
    public function __construct(ModflowModelList $modelList, GeoTools $geoTools)
    {
        $this->modelList = $modelList;
        $this->geoTools = $geoTools;
    }

    public function __invoke(UpdateAreaGeometry $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $modflowModel->updateAreaGeometry($command->userId(), $command->geometry());
    }
}
