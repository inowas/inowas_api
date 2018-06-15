<?php

declare(strict_types=1);

namespace Inowas\Tool\Model\Handler;

use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\Name;
use Inowas\ModflowBundle\Exception\AccessDeniedException;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\Tool\Model\Command\UpdateToolInstance;
use Inowas\Tool\Model\ToolData;
use Inowas\Tool\Model\ToolInstanceAggregate;
use Inowas\Tool\Model\ToolInstanceList;

/**
 * Class UpdateToolInstanceHandler
 * @package Inowas\Tool\Model\Handler
 */
final class UpdateToolInstanceHandler
{
    /** @var  ToolInstanceList */
    private $toolInstanceList;

    public function __construct(ToolInstanceList $toolInstanceList)
    {
        $this->toolInstanceList = $toolInstanceList;
    }

    /**
     * @param UpdateToolInstance $command
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     * @throws AccessDeniedException
     */
    public function __invoke(UpdateToolInstance $command)
    {
        /** @var ToolInstanceAggregate $toolInstance */
        $toolInstance = $this->toolInstanceList->get($command->id());

        if (! $toolInstance) {
            throw NotFoundException::withMessage(
                sprintf('ToolInstance with id=%s not found', $command->id()->toString())
            );
        }

        if (! $toolInstance->userId()->sameValueAs($command->userId())) {
            throw AccessDeniedException::withMessage(
                sprintf('User with id=%s does not have sufficient access to tool with id=%s',
                    $command->userId()->toString(), $command->id()->toString())
            );
        }

        if ($command->name() instanceof Name) {
            $toolInstance->updateName($command->userId(), $command->name());
        }

        if ($command->description() instanceof Description) {
            $toolInstance->updateDescription($command->userId(), $command->description());
        }

        if ($command->data() instanceof ToolData) {
            $toolInstance->updateData($command->userId(), $command->data());
        }

        if  ($command->visibility()) {
            $toolInstance->changeVisibility($command->userId(), $command->visibility());
        }

        $this->toolInstanceList->save($toolInstance);
    }
}
