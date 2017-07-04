<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Handler;

use Inowas\ModflowBoundary\Model\Command\CloneBoundary;
use Inowas\ModflowBoundary\Model\Exception\ModflowBoundaryNotFoundException;
use Inowas\ModflowBoundary\Model\ModflowBoundaryAggregate;
use Inowas\ModflowBoundary\Model\ModflowBoundaryList;

final class CloneBoundaryHandler
{
    /** @var  \Inowas\ModflowBoundary\Model\ModflowBoundaryList */
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
