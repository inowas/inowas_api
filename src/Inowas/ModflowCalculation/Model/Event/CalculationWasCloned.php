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
class CalculationWasCloned extends AggregateChanged
{
    /** @var UserId */
    private $userId;

    /** @var ModflowId */
    private $calculationId;

    /** @var ModflowId */
    private $fromCalculationId;

    /** @var ModflowId */
    private $modflowmodelId;

    /** @var DateTime */
    private $start;

    /** @var DateTime */
    private $end;

    /** @var LengthUnit */
    private $lengthUnit;

    /** @var TimeUnit */
    private $timeUnit;

    /** @var StressPeriods */
    private $stressPeriods;


    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $oldCalculationId
     * @param ModflowId $newCalculationId
     * @param ModflowId $modflowModelId
     * @param DateTime $start
     * @param DateTime $end
     * @param LengthUnit $lengthUnit
     * @param TimeUnit $timeUnit
     * @param StressPeriods $stressPeriods
     * @return CalculationWasCloned
     * @internal param ModflowId $oldModelId
     * @internal param ModflowCalculationConfiguration $configuration
     * @internal param ModflowId $calculationId
     * @internal param ModflowId $modflowModelId
     * @internal param SoilmodelId $soilModelId
     */
    public static function byUserWithIds(
        UserId $userId,
        ModflowId $oldCalculationId,
        ModflowId $newCalculationId,
        ModflowId $modflowModelId,
        DateTime $start,
        DateTime $end,
        LengthUnit $lengthUnit,
        TimeUnit $timeUnit,
        StressPeriods $stressPeriods
    ): CalculationWasCloned
    {
        $event = self::occur($newCalculationId->toString(),[
            'user_id' => $userId->toString(),
            'from_calculation_id' => $oldCalculationId->toString(),
            'modflowmodel_id' => $modflowModelId->toString(),
            'start' => $start->toAtom(),
            'end' => $end->toAtom(),
            'length_unit' => $lengthUnit->toInt(),
            'time_unit' => $timeUnit->toInt(),
            'stress_periods' => serialize($stressPeriods)
        ]);

        $event->calculationId = $newCalculationId;
        $event->userId = $userId;
        $event->fromCalculationId = $oldCalculationId;
        $event->modflowmodelId = $modflowModelId;
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

    public function userId(): UserId{
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function fromCalculationId(): ModflowId
    {
        if ($this->fromCalculationId === null){
            $this->fromCalculationId = ModflowId::fromString($this->payload['from_calculation_id']);
        }

        return $this->fromCalculationId;
    }

    public function modflowmodelId(): ModflowId
    {
        if ($this->modflowmodelId === null){
            $this->modflowmodelId = ModflowId::fromString($this->payload['modflowmodel_id']);
        }

        return $this->modflowmodelId;
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
        if (null === $this->stressPeriods) {
            $this->stressPeriods = unserialize($this->payload['stress_periods'], [StressPeriods::class, DateTime::class]);
        }

        return $this->stressPeriods;
    }
}
