<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\ModflowModel;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\TimeUnit;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CalculateStressPeriods extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param DateTime $start
     * @param DateTime $end
     * @param TimeUnit $timeUnit
     * @return CalculateStressPeriods
     */
    public static function forModflowModel(UserId $userId, ModflowId $modelId, DateTime $start, DateTime $end, TimeUnit $timeUnit): CalculateStressPeriods
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'start' => $start->toAtom(),
                'end' => $end->toAtom(),
                'time_unit' => $timeUnit->toInt(),
                'initial_steady' => true
            ]
        );
    }

    public function modflowId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflow_model_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function start(): DateTime
    {
        return DateTime::fromAtom($this->payload['start']);
    }

    public function end(): DateTime
    {
        return DateTime::fromAtom($this->payload['end']);
    }

    public function timeUnit(): TimeUnit
    {
        return TimeUnit::fromInt($this->payload['time_unit']);
    }

    public function withInitialSteadyStressPeriod(): bool
    {
        return $this->payload['initial_steady'];
    }
}
