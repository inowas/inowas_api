<?php

declare(strict_types=1);

namespace Inowas\Common\Calculation;

class CalculationState
{
    public const NEW = 0;
    public const CALCULATION_PROCESS_STARTED = 1;
    public const PREPROCESSING = 2;
    public const PREPROCESSING_FINISHED = 3;
    public const QUEUED = 3;
    public const CALCULATING = 4;
    public const FINISHED = 5;

    /** @var  int */
    private $state;

    public static function new(): CalculationState
    {
        return new self(self::NEW);
    }

    public static function calculationProcessStarted(): CalculationState
    {
        return new self(self::CALCULATION_PROCESS_STARTED);
    }

    public static function preprocessing(): CalculationState
    {
        return new self(self::PREPROCESSING);
    }

    public static function preprocessingFinished(): CalculationState
    {
        return new self(self::PREPROCESSING_FINISHED);
    }

    public static function queued(): CalculationState
    {
        return new self(self::QUEUED);
    }

    public static function calculating(): CalculationState
    {
        return new self(self::CALCULATING);
    }

    public static function finished(): CalculationState
    {
        return new self(self::FINISHED);
    }

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

    public function isFinished(): bool
    {
        return $this->state === self::FINISHED;
    }
}
