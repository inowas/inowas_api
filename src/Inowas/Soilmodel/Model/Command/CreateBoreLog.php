<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\BoreLogId;
use Inowas\Soilmodel\Model\BoreLogLocation;
use Inowas\Soilmodel\Model\BoreLogName;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateBoreLog extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUser(UserId $userId, BoreLogId $boreLogId, BoreLogName $name, BoreLogLocation $location): CreateBoreLog
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'borelog_id' => $boreLogId->toString(),
                'name' => $name->toString(),
                'location' => $location->toArray(),
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

    public function name(): BoreLogName
    {
        return BoreLogName::fromString($this->payload['name']);
    }

    public function location(): BoreLogLocation
    {
        return BoreLogLocation::fromArray($this->payload['location']);
    }
}
