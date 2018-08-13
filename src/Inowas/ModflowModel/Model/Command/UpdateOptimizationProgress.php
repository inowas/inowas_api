<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\OptimizationProgress;

class UpdateOptimizationProgress extends AbstractJsonSchemaCommand
{
    /**
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param OptimizationProgress $progress
     * @return self
     * @throws \League\JsonGuard\Exception\MaximumDepthExceededException
     * @throws \League\JsonGuard\Exception\InvalidSchemaException
     * @throws \InvalidArgumentException
     * @throws \Inowas\Common\Exception\JsonSchemaValidationFailedException
     */
    public static function forModflowModel(UserId $userId, ModflowId $modelId, OptimizationProgress $progress): self
    {
        $self = new static(
            array(
                'id' => $modelId->toString(),
                'progress' => $progress->toArray()
            )
        );

        /** @var self $self */
        $self = $self->withAddedMetadata('user_id', $userId->toString());
        return $self;
    }

    public function schema(): string
    {
        return 'file://spec/schema/modflow/command/optimizationInputPayload.json';
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
     * @return OptimizationProgress
     * @throws \Exception
     */
    public function progress(): OptimizationProgress
    {
        return OptimizationProgress::fromArray($this->payload['progress']);
    }
}
