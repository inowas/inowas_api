<?php

namespace Inowas\ModflowModel\Model\Exception;

use Inowas\Common\Id\ModflowId;

final class ModflowModelOptimizationFailedException extends \InvalidArgumentException
{
    public static function withModelId(ModflowId $modflowId, ModflowId $optimizationId): self
    {
        return new self(sprintf(
            'Model Optimization failed. Model-Id: %s. Optimization-Id: %s',
            $modflowId->toString(),
            $optimizationId->toString()
        ));
    }
}
