<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\ModflowModel;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\TimeUnit;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateTimeUnit extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserAndModel(
        UserId $userId,
        ModflowId $baseModelId,
        TimeUnit $timeUnit
    ): UpdateTimeUnit
    {
        $payload = [
            'user_id' => $userId->toString(),
            'model_id' => $baseModelId->toString(),
            'time_unit' => $timeUnit->toInt()
        ];

        return new self($payload);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['model_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function timeUnit(): TimeUnit
    {
        return TimeUnit::fromInt($this->payload['time_unit']);
    }
}
