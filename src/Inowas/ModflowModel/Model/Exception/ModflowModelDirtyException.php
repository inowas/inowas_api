<?php

namespace Inowas\ModflowModel\Model\Exception;

use Inowas\Common\Id\ModflowId;

final class ModflowModelDirtyException extends \InvalidArgumentException
{
    public static function withModelId(ModflowId $modflowId): self
    {
        return new self(sprintf('Model wit Id: %s is dirty, please calculate first.', $modflowId->toString()));
    }
}
