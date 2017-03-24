<?php

namespace Inowas\Soilmodel\Model\Exception;

use Inowas\Soilmodel\Model\BoreLogId;

final class BoreLogNotFoundException extends \InvalidArgumentException
{
    public static function withBoreLogId(BoreLogId $id): BoreLogNotFoundException
    {
        return new self(sprintf('BoreLog with id %s cannot be found.', $id->toString()));
    }
}
