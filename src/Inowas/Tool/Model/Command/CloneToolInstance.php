<?php

declare(strict_types=1);

namespace Inowas\Tool\Model\Command;

use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\UserId;
use Inowas\Tool\Model\ToolId;

class CloneToolInstance extends AbstractJsonSchemaCommand
{
    public function schema(): string
    {
        return 'file://spec/schema/tool/command/cloneToolInstancePayload.json';
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ToolId $baseId
     * @param ToolId $newId
     * @return CloneToolInstance
     */
    public static function newWithAllParams(
        UserId $userId,
        ToolId $baseId,
        ToolId $newId
    ): CloneToolInstance
    {
        $self = new static(
            [
                'id' => $newId->toString(),
                'base_id' => $baseId->toString()
            ]
        );

        /** @var CloneToolInstance $self */
        $self = $self->withAddedMetadata('user_id', $userId->toString());
        return $self;
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->metadata['user_id']);
    }

    public function baseId(): ToolId
    {
        return ToolId::fromString($this->payload['base_id']);
    }

    public function id(): ToolId
    {
        return ToolId::fromString($this->payload['id']);
    }
}
