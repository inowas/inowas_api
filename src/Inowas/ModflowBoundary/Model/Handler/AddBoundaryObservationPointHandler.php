<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Handler;

use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\ModflowBoundary\Model\Command\AddBoundaryObservationPoint;
use Inowas\ModflowBoundary\Model\Exception\ModflowBoundaryNotFoundException;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowBoundary\Model\ModflowBoundaryAggregate;
use Inowas\ModflowBoundary\Model\ModflowBoundaryList;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowBoundary\Service\BoundaryManager;

final class AddBoundaryObservationPointHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  \Inowas\ModflowBoundary\Model\ModflowBoundaryList */
    private $boundaryList;

    /** @var  BoundaryManager */
    private $boundaryManager;


    /**
     * AddBoundaryObservationPointHandler constructor.
     * @param ModflowModelList $modelList
     * @param \Inowas\ModflowBoundary\Model\ModflowBoundaryList $boundaryList
     */
    public function __construct(ModflowModelList $modelList, ModflowBoundaryList $boundaryList)
    {
        $this->boundaryList = $boundaryList;
        $this->modelList = $modelList;
    }

    public function __invoke(AddBoundaryObservationPoint $command)
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

        $boundaryType = $this->boundaryManager->getBoundaryTypeByBoundaryId($command->boundaryId());

        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $command->observationPointId(),
            $boundaryType,
            $command->observationPointName(),
            $command->geometry()
        );

        $boundary->addObservationPoint($command->userId(), $observationPoint);
    }
}
