<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\BoreLogLocation;
use Inowas\Common\Soilmodel\BoreLogId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeBoreLogLocation extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithId(UserId $userId, BoreLogId $boreLogId, BoreLogLocation $location): ChangeBoreLogLocation
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'borelog_id' => $boreLogId->toString(),
                'location' => $location->toArray()
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

    public function location(): BoreLogLocation
    {
        return BoreLogLocation::fromArray($this->payload['location']);
    }
}
