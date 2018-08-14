<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;

class CalculateOptimization extends AbstractJsonSchemaCommand
{
    /**
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param ModflowId $optimizationId
     * @return self
     */
    public static function forModflowModel(UserId $userId, ModflowId $modelId, ModflowId $optimizationId = null): self
    {
        if (null === $optimizationId) {
            $optimizationId = $modelId;
        }

        $self = new static(
            array(
                'id' => $modelId->toString(),
                'optimization_id' => $optimizationId->toString()
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
