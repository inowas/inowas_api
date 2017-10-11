<?php

declare(strict_types=1);

namespace Inowas\Tool\Model\Handler;

use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\Tool\Model\Command\CloneToolInstance;
use Inowas\Tool\Model\ToolInstanceAggregate;
use Inowas\Tool\Model\ToolInstanceList;

final class CloneToolInstanceHandler
{

    /** @var  ToolInstanceList */
    private $toolInstanceList;

    public function __construct(ToolInstanceList $toolInstanceList)
    {
        $this->toolInstanceList = $toolInstanceList;
    }

    public function __invoke(CloneToolInstance $command)
    {
        /** @var ToolInstanceAggregate $toolInstance */
        $toolInstance = $this->toolInstanceList->get($command->baseId());

        if (! $toolInstance) {
            throw NotFoundException::withMessage(
                sprintf('ToolInstance with id=%s not found', $command->baseId()->toString())
            );
        }

        $newToolInstance = $toolInstance->clone($command->userId(), $command->id());
        $this->toolInstanceList->save($newToolInstance);
    }
}
