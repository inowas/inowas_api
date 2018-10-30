<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;

class CalculateOptimization extends AbstractJsonSchemaCommand
{
    /**
     * @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param ModflowId $optimizationId
     * @param bool $isInitial
     * @return self
     */
    public static function forModflowModel(
        UserId $userId,
        ModflowId $modelId,
        ModflowId $optimizationId = null,
        bool $isInitial = true
    ): self
    {
        if (null === $optimizationId) {
            $optimizationId = $modelId;
        }

        $self = new static([
            'id' => $modelId->toString(),
            'optimization_id' => $optimizationId->toString(),
            'is_initial' => $isInitial
        ]);

        /** @var self $self */
        $self = $self->withAddedMetadata('user_id', $userId->toString());
        return $self;
    }

    public function schema(): string
    {
        return 'file://spec/schema/modflow/command/calculateOptimizationPayload.json';
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->metadata['user_id']);
    }
    
    public function optimizationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['optimization_id']);
    }

    public function isInitial(): bool
    {
        return $this->payload['is_initial'];
    }
}
