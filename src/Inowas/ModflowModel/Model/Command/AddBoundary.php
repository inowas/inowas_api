<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Boundaries\BoundaryFactory;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;

class AddBoundary extends AbstractJsonSchemaCommand
{

    /**
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param ModflowBoundary $boundary
     * @return AddBoundary
     * @throws \League\JsonGuard\Exception\MaximumDepthExceededException
     * @throws \League\JsonGuard\Exception\InvalidSchemaException
     * @throws \InvalidArgumentException
     * @throws \Inowas\Common\Exception\JsonSchemaValidationFailedException
     */
    public static function forModflowModel(UserId $userId, ModflowId $modelId, ModflowBoundary $boundary): AddBoundary
    {
        $self = new static(
            array(
                'id' => $modelId->toString(),
                'boundary' => $boundary->toArray()
            )
        );

        /** @var AddBoundary $self */
        $self = $self->withAddedMetadata('user_id', $userId->toString());
        return $self;
    }

    public function schema(): string
    {
        return 'file://spec/schema/modflow/command/addBoundaryPayload.json';
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->metadata['user_id']);
    }

    /**
     * @return ModflowBoundary
     * @throws \Exception
     */
    public function boundary(): ModflowBoundary
    {
        return BoundaryFactory::createFromArray($this->payload['boundary']);
    }
}
