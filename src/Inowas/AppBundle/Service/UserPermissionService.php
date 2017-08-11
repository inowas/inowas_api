<?php

declare(strict_types=1);

namespace Inowas\AppBundle\Service;

use Inowas\AppBundle\Model\UserPermission;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Tool\Infrastructure\Projection\ToolFinder;
use Inowas\Tool\Model\ToolId;

class UserPermissionService
{

    /** @var  ToolFinder */
    private $toolFinder;

    public function __construct(ToolFinder $toolFinder)
    {
        $this->toolFinder = $toolFinder;
    }

    public function getModelPermissions(UserId $userId, ModflowId $modelId): UserPermission
    {
        if ($this->toolFinder->isToolOwner(ToolId::fromString($modelId->toString()), $userId)){
            return UserPermission::readWriteExecute();
        }

        if ($this->toolFinder->isPublic(ToolId::fromString($modelId->toString()))) {
            return UserPermission::readOnly();
        }

        return UserPermission::noPermission();
    }
}
