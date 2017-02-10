<?php

namespace Inowas\Modflow\Model\Exception;

use Inowas\Modflow\Model\ModflowModelId;

final class ModflowModelNotFoundException extends \InvalidArgumentException
{
    public static function withModelId(ModflowModelId $modelId)
    {
        return new self(sprintf('ModflowModel with id %s cannot be found.', $modelId->toString()));
    }
}
