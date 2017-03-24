<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

interface BoreLogList
{
    public function add(BoreLogAggregate $boreLog);

    public function get(BoreLogId $boreLogId);
}
