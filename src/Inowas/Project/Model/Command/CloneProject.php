<?php

declare(strict_types=1);

namespace Inowas\Project\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\Project\Model\ProjectId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CloneProject extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function fromProjectByUser(ProjectId $projectId, UserId $userId): CloneProject
    {
        return new self([
            'project_id' => $projectId->toString(),
            'user_id' => $userId->toString()
        ]);
    }

    public function projectId(): ProjectId
    {
        return ProjectId::fromString($this->payload['project_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }
}
