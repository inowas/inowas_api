<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Handler;

use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\ModflowBoundary\Model\Exception\ModflowBoundaryAlreadyExistsException;
use Inowas\ModflowBoundary\Model\ModflowBoundaryAggregate;
use Inowas\ModflowBoundary\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowBoundary\Model\ModflowBoundaryList;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class AddBoundaryHandler
{

    /** @var  ModflowBoundaryList */
    private $boundaryList;

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * AddBoundaryHandler constructor.
     * @param ModflowModelList $modelList
     * @param \Inowas\ModflowBoundary\Model\ModflowBoundaryList $boundaryList
     */
    public function __construct(ModflowModelList $modelList, ModflowBoundaryList $boundaryList)
    {
        $this->boundaryList = $boundaryList;
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

        $b = $command->boundary();
        $boundary = $this->boundaryList->get($b->boundaryId());

        if ($boundary instanceof ModflowBoundaryAggregate) {
            throw ModflowBoundaryAlreadyExistsException::withId($b->boundaryId());
        }

        $boundary = ModflowBoundaryAggregate::create(
            $b->boundaryId(),
            $command->modelId(),
            $command->userId(),
            $b->type(),
            $b->name(),
            $b->geometry(),
            $b->affectedLayers(),
            $b->metadata()
        );

        /** @var ObservationPoint $observationPoint */
        foreach ($b->observationPoints()->toArray() as $observationPoint) {
            $boundary->addObservationPoint($command->userId(), $observationPoint);
        }

        $this->boundaryList->add($boundary);
    }
}
