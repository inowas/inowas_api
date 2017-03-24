<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

class BoreLog
{
    /** @var  BoreLogId */
    protected $id;

    public function id(): BoreLogId
    {
        return $this->id;
    }
}
