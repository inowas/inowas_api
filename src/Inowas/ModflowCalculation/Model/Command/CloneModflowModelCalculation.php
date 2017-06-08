<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CloneModflowModelCalculation extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $oldCalculationId
     * @param ModflowId $newCalculationId
     * @param ModflowId $oldModelId
     * @param ModflowId $newModelId
     * @return CloneModflowModelCalculation
     */
    public static function byUserWithModelId(
        UserId $userId,
        ModflowId $oldCalculationId,
        ModflowId $newCalculationId,
        ModflowId $oldModelId,
        ModflowId $newModelId
    ): CloneModflowModelCalculation
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'old_calculation_id' => $oldCalculationId->toString(),
                'new_calculation_id' => $newCalculationId->toString(),
                'old_model_id' => $oldModelId->toString(),
                'new_model_id' => $newModelId->toString()
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function oldCalculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['old_calculation_id']);
    }

    public function newCalculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['new_calculation_id']);
    }

    public function oldModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['old_model_id']);
    }

    public function newModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['new_model_id']);
    }
}
