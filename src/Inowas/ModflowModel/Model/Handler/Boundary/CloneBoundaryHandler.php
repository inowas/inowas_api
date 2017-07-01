<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler\Boundary;

use Inowas\ModflowModel\Model\Command\Boundary\CloneBoundary;
use Inowas\ModflowModel\Model\Exception\ModflowBoundaryNotFoundException;
use Inowas\ModflowModel\Model\ModflowBoundaryAggregate;
use Inowas\ModflowModel\Model\ModflowBoundaryList;

final class CloneBoundaryHandler
{
    /** @var  ModflowBoundaryList */
    private $boundaryList;

    public function __construct(ModflowBoundaryList $boundaryList)
    {
        $this->boundaryList = $boundaryList;
    }

    public function __invoke(CloneBoundary $command)
    {
        /** @var ModflowBoundaryAggregate $boundary */
        $boundary = $this->boundaryList->get($command->existentId());

        if (!$boundary){
            throw ModflowBoundaryNotFoundException::withId($command->existentId());
        }

        $newBoundary = ModflowBoundaryAggregate::clone($command->cloneId(), $command->modelId(), $boundary);
        $this->boundaryList->add($newBoundary);
    }
}
