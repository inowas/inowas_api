<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class TimeUnit
{

    /** @var array $availableTimeUnits */
    public $availableTimeUnits = array(1,2,3,4,5);

    const UNDEFINED = 0;
    const SECONDS = 1;
    const MINUTES = 2;
    const HOURS = 3;
    const DAYS = 4;
    const YEARS = 5;

    protected $itmuni;

    public static function fromString(string $timeUnit): TimeUnit
    {
        switch ($timeUnit) {
            case "sec":
                return new self(1);
                break;
            case "min":
                return new self(2);
                break;
            case "h":
                return new self(3);
                break;
            case "d":
                return new self(4);
                break;
            case "yr":
                return new self(5);
                break;
        }

        return new self(4);
    }

    public static function fromInt(int $itmuni): TimeUnit
    {
        return new self($itmuni);
    }

    public static function fromValue(int $itmuni): TimeUnit
    {
        return new self($itmuni);
    }

    private function __construct(int $itmuni)
    {
        $this->itmuni = $itmuni;
    }

    public function toValue(): int
    {
        return $this->itmuni;
    }

    public function toInt(): int
    {
        return $this->itmuni;
    }

    public function sameAs(TimeUnit $timeUnit): bool
    {
        return $this->itmuni == $timeUnit->toInt();
    }
}
