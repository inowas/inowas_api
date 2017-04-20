<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\StressPeriods;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateCalculationStressperiods extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithCalculationId(
        UserId $userId,
        ModflowId $calculationId,
        StressPeriods $stressPeriods
    ): UpdateCalculationStressperiods
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'calculation_id' => $calculationId->toString(),
                'stress_periods' => serialize($stressPeriods)
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

    public function stressPeriods()
    {
        return unserialize($this->payload['stress_periods']);
    }
}
