<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowModel\Model\Exception\InvalidTimeUnitException;

class HeadObservationWellDateTimeValue extends DateTimeValue
{

    public const TYPE = 'hob';

    /** @var float */
    private $head;


    public static function fromParams(DateTime $dateTime, float $head): HeadObservationWellDateTimeValue
    {
        return new self($dateTime, $head);
    }

    /**
     * @param array $arr
     * @return HeadObservationWellDateTimeValue
     * @throws \Exception
     */
    public static function fromArray(array $arr): HeadObservationWellDateTimeValue
    {
        return new self(DateTime::fromAtom($arr['date_time']), $arr['values'][0]);
    }

    /**
     * @param array $arr
     * @return HeadObservationWellDateTimeValue
     * @throws \Exception
     */
    public static function fromArrayValues(array $arr): HeadObservationWellDateTimeValue
    {
        return new self(DateTime::fromAtom($arr[0]), $arr[1]);
    }

    private function __construct(DateTime $dateTime, float $head) {
        $this->dateTime = $dateTime;
        $this->head = $head;
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function dateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function head(): float
    {
        return $this->head;
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->toAtom(),
            'values' => [$this->head]
        );
    }

    public function values(): array
    {
        return array(
            'head' => $this->head
        );
    }

    /**
     * @param DateTime $startDateTime
     * @param TimeUnit $timeUnit
     * @return TotalTime
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidTimeUnitException
     */
    public function getTotalTime(DateTime $startDateTime, TimeUnit $timeUnit): TotalTime
    {
        /** @var \DateTime $start */
        $start = clone $startDateTime->toDateTime();

        /** @var \DateTime $dateTime */
        $dateTime = clone $this->dateTime->toDateTime();

        $diff = $start->diff($dateTime);

        if ($timeUnit->toInt() === $timeUnit::SECONDS){
            return TotalTime::fromInt($dateTime->getTimestamp() - $start->getTimestamp());
        }

        if ($timeUnit->toInt() === $timeUnit::MINUTES){
            return TotalTime::fromInt((int)(($dateTime->getTimestamp() - $start->getTimestamp())/60));
        }

        if ($timeUnit->toInt() === $timeUnit::HOURS){
            return TotalTime::fromInt((int)(($dateTime->getTimestamp() - $start->getTimestamp())/60/60));
        }

        if ($timeUnit->toInt() === $timeUnit::DAYS){
            return TotalTime::fromInt((int)$diff->format('%a'));
        }

        throw InvalidTimeUnitException::withTimeUnitAndAvailableTimeUnits($timeUnit, $timeUnit->availableTimeUnits);
    }

    /**
     * @return float
     */
    public function getHead(): float
    {
        return $this->head;
    }
}
