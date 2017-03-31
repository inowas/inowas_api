<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\IdInterface;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelWasCreated extends AggregateChanged
{

    /** @var  \Inowas\Common\Id\IdInterface */
    private $modflowModelId;

    /** @var  UserId */
    private $userId;

    /** @var  LengthUnit */
    private $lengthUnit;

    /** @var  TimeUnit */
    private $timeUnit;

    public static function byUserWithModflowIdAndUnits(UserId $userId, IdInterface $modflowModelId, LengthUnit $lengthUnit, TimeUnit $timeUnit): ModflowModelWasCreated
    {
        $event = self::occur($modflowModelId->toString(),[
            'user_id' => $userId->toString(),
            'length_unit' => $lengthUnit->toInt(),
            'time_unit' => $timeUnit->toInt()
        ]);

        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;

        return $event;
    }

    public function modflowModelId(): IdInterface
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function lengthUnit(): LengthUnit
    {
        if ($this->lengthUnit === null){
            $this->lengthUnit = LengthUnit::fromInt($this->payload['length_unit']);
        }

        return $this->lengthUnit;
    }

    public function timeUnit(): TimeUnit
    {
        if ($this->timeUnit === null){
            $this->timeUnit = TimeUnit::fromInt($this->payload['time_unit']);
        }

        return $this->timeUnit;
    }
}
