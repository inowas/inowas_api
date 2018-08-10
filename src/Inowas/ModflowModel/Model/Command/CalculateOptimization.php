<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Optimization;

class CalculateOptimization extends AbstractJsonSchemaCommand
{
    /**
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param Optimization $optimization
     * @return self
     * @throws \League\JsonGuard\Exception\MaximumDepthExceededException
     * @throws \League\JsonGuard\Exception\InvalidSchemaException
     * @throws \InvalidArgumentException
     * @throws \Inowas\Common\Exception\JsonSchemaValidationFailedException
     */
    public static function forModflowModel(UserId $userId, ModflowId $modelId, Optimization $optimization): self
    {
        $self = new static(
            array(
                'id' => $modelId->toString(),
                'optimization_id' => $optimization->toArray()
            )
        );

        /** @var self $self */
        $self = $self->withAddedMetadata('user_id', $userId->toString());
        return $self;
    }

    public function schema(): string
    {
        return 'file://spec/schema/modflow/command/optimizationIdPayload.json';
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
     * @return ModflowId
     * @throws \Exception
     */
    public function optimizationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['optimization_id']);
    }
}
