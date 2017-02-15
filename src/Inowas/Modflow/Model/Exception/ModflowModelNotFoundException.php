<?php

namespace Inowas\Modflow\Model\Exception;

use Inowas\Modflow\Model\ModflowId;

final class ModflowModelNotFoundException extends \InvalidArgumentException
{
    public static function withModelId(ModflowId $modelId)
    {
        return new self(sprintf('ModflowModel with id %s cannot be found.', $modelId->toString()));
    }
}
