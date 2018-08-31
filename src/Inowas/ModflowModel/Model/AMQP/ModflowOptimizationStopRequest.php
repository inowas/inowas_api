<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Id\ModflowId;

class ModflowOptimizationStopRequest implements \JsonSerializable
{
    private $type = 'optimization_stop';

    /** @var  ModflowId */
    private $modelId;

    /** @var  ModflowId */
    private $optimizationId;

    public static function stopOptimization(
        ModflowId $modelId,
        ModflowId $optimizationId
    ): self
    {
        $self = new self();
        $self->modelId = $modelId;
        $self->optimizationId = $optimizationId;
        return $self;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'model_id' => $this->modelId->toString(),
            'optimization_id' => $this->optimizationId->toString(),
        ];
    }

    private function __construct()
    {}
}
