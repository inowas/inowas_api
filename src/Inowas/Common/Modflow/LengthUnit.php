<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class LengthUnit
{
    
    const UNDEFINED = 0;
    const FEET = 1;
    const METERS = 2;
    const CENTIMETERS = 3;

    protected $lenuni;

    public static function fromInt(int $lenuni): LengthUnit
    {
        return new self($lenuni);
    }

    public static function fromValue(int $lenuni): LengthUnit
    {
        return new self($lenuni);
    }

    public static function fromString(string $lenuni): LengthUnit
    {
        switch ($lenuni){
            case "ft":
                return new self(1);
                break;
            case "m":
                return new self(2);
                break;
            case "cm":
                return new self(3);
                break;
        }

        return new self(2);
    }

    private function __construct(int $lenuni)
    {
        $this->lenuni = $lenuni;
    }

    public function toValue(): int
    {
        return $this->lenuni;
    }

    public function toInt(): int
    {
        return $this->lenuni;
    }

    public function sameAs(LengthUnit $lengthUnit): bool
    {
        return $this->lenuni == $lengthUnit->toInt();
    }
}
