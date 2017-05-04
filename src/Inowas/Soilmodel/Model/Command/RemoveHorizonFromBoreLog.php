<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\BoreLogId;
use Inowas\Common\Soilmodel\HorizonId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class RemoveHorizonFromBoreLog extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithId(UserId $userId, BoreLogId $boreLogId, HorizonId $horizonId): RemoveHorizonFromBoreLog
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'borelog_id' => $boreLogId->toString(),
                'horizon_id' => $horizonId->toString()
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function boreLogId(): BoreLogId
    {
        return BoreLogId::fromString($this->payload['borelog_id']);
    }

    public function horizonId(): HorizonId
    {
        return HorizonId::fromString($this->payload['horizon_id']);
    }
}
