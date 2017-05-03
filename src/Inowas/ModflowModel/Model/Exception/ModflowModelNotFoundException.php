<?php

namespace Inowas\ModflowModel\Model\Exception;

use Inowas\Common\Id\ModflowId;

final class ModflowModelNotFoundException extends \InvalidArgumentException
{
    public static function withModelId(ModflowId $modelId)
    {
        return new self(sprintf('ModflowModel with id %s cannot be found.', $modelId->toString()));
    }

    public static function withScenarioId(ModflowId $baseModelId, ModflowId $scenarioId)
    {
        return new self(sprintf('ModflowScenario with id %s cannot be found in BaseModel with id %s.', $scenarioId->toString(), $baseModelId->toString()));
    }
}
