<?php

declare(strict_types=1);

namespace Inowas\Tool\Model\Command;

use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\Name;
use Inowas\Tool\Model\ToolData;
use Inowas\Tool\Model\ToolId;

class UpdateToolInstance extends AbstractJsonSchemaCommand
{
    public function schema(): string
    {
        return 'file://spec/schema/tool/command/updateToolInstancePayload.json';
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ToolId $id
     * @param Name $name
     * @param Description $description
     * @param ToolData $data
     * @return UpdateToolInstance
     */
    public static function newWithAllParams(
        UserId $userId,
        ToolId $id,
        ?Name $name = null,
        ?Description $description = null,
        ?ToolData $data = null
    ): UpdateToolInstance
    {
        $self = new static(
            [
                'id' => $id->toString(),
                'name' => ($name instanceof Name) ? $name->toString() : null,
                'description' => ($description instanceof Description) ? $description->toString() : null,
                'data' => ($data instanceof ToolData) ? $data->toArray() : null
            ]
        );

        /** @var UpdateToolInstance $self */
        $self = $self->withAddedMetadata('user_id', $userId->toString());
        return $self;
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->metadata['user_id']);
    }

    public function id(): ToolId
    {
        return ToolId::fromString($this->payload['id']);
    }

    public function name(): ?Name
    {
        if (null === $this->payload['name']) {
            return null;
        }

        return Name::fromString($this->payload['name']);
    }

    public function description(): ?Description
    {
        if (null === $this->payload['description']) {
            return null;
        }

        return Description::fromString($this->payload['description']);
    }

    public function data(): ?ToolData
    {
        if (null === $this->payload['data']) {
            return null;
        }

        return ToolData::fromArray($this->payload['data']);
    }
}
