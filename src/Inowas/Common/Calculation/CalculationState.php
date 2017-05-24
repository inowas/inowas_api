<?php

declare(strict_types=1);

namespace Inowas\Common\Calculation;

class CalculationState
{
    const CREATED = 0;
    const QUEUED = 1;
    const STARTED = 2;
    const FINISHED = 3;

    /** @var  int */
    private $state;

    public static function fromInt(int $state): CalculationState
    {
        return new self($state);
    }

    private function __construct(int $state)
    {
        $this->state = $state;
    }


    public function toInt(): int
    {
        return $this->state;
    }
}
