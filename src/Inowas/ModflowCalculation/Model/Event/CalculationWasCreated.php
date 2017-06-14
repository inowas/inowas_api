<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Event;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;

use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class CalculationWasCreated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  ModflowId */
    private $modflowModelId;

    /** @var UserId */
    private $userId;

    /** @var DateTime */
    private $start;

    /** @var DateTime */
    private $end;

    /** @var LengthUnit */
    private $lengthUnit;

    /** @var TimeUnit */
    private $timeUnit;

    /** @var  StressPeriods */
    private $stressPeriods;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $calculationId
     * @param ModflowId $modflowModelId
     * @param DateTime $start
     * @param DateTime $end
     * @param LengthUnit $lengthUnit
     * @param TimeUnit $timeUnit
     * @param StressPeriods $stressPeriods
     * @return CalculationWasCreated
     */
    public static function fromModelWithProps(
        UserId $userId,
        ModflowId $calculationId,
        ModflowId $modflowModelId,
        DateTime $start,
        DateTime $end,
        LengthUnit $lengthUnit,
        TimeUnit $timeUnit,
        StressPeriods $stressPeriods
    ): CalculationWasCreated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'modflowmodel_id' => $modflowModelId->toString(),
            'start' => $start->toAtom(),
            'end' => $end->toAtom(),
            'length_unit' => $lengthUnit->toInt(),
            'time_unit' => $timeUnit->toInt(),
            'stress_periods' => serialize($stressPeriods)
        ]);

        $event->calculationId = $calculationId;
        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;
        $event->start = $start;
        $event->end = $end;
        $event->lengthUnit = $lengthUnit;
        $event->timeUnit = $timeUnit;
        $event->stressPeriods = $stressPeriods;

        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function modflowmodelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->payload['modflowmodel_id']);
        }

        return $this->modflowModelId;
    }

    public function userId(): UserId{
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function start(): DateTime
    {
        if ($this->start === null){
            $this->start = DateTime::fromAtom($this->payload['start']);
        }

        return $this->start;
    }

    public function end(): DateTime
    {
        if ($this->end === null){
            $this->end = DateTime::fromAtom($this->payload['end']);
        }

        return $this->end;
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

    public function stressPeriods(): StressPeriods
    {
        if (null === $this->stressPeriods){
            $this->stressPeriods = unserialize($this->payload['stress_periods'], [StressPeriods::class, DateTime::class]);
        }

        return $this->stressPeriods;
    }
}
