<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateCalculationWithChangedBoundaries extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithCalculationId(
        UserId $userId,
        ModflowId $calculationId
    ): UpdateCalculationWithChangedBoundaries
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'calculation_id' => $calculationId->toString()
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function calculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['calculation_id']);
    }
}
