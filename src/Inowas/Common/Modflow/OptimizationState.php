<?php

namespace Inowas\Common\Modflow;


class OptimizationState
{
    public const NEW = 0;
    public const STARTED = 1;
    public const PREPROCESSING = 2;
    public const PREPROCESSING_FINISHED = 3;
    public const QUEUED = 4;
    public const CALCULATING = 5;
    public const FINISHED = 6;

    public const CANCELLING = 11;
    public const CANCELLED = 12;

    public const ERROR_RECALCULATING_MODEL = 40;
    public const ERROR_PUBLISHING = 41;

    /** @var int */
    private $state;

    /**
     * @param int $state
     * @return self
     */
    public static function fromInt(int $state): self
    {
        return new self($state);
    }

    public static function started(): self
    {
        return new self(self::STARTED);
    }

    public static function preprocessing(): self
    {
        return new self(self::PREPROCESSING);
    }

    public static function preprocessingFinished(): self
    {
        return new self(self::PREPROCESSING_FINISHED);
    }

    public static function queued(): self
    {
        return new self(self::QUEUED);
    }

    public static function calculating(): self
    {
        return new self(self::CALCULATING);
    }

    public static function finished(): self
    {
        return new self(self::FINISHED);
    }

    public static function cancelling(): self
    {
        return new self(self::CANCELLING);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public static function errorRecalculatingModel(): self
    {
        return new self(self::ERROR_RECALCULATING_MODEL);
    }

    public static function errorPublishing(): self
    {
        return new self(self::ERROR_PUBLISHING);
    }

    /**
     * Optimization constructor.
     * @param $state
     */
    private function __construct($state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return $this->state;
    }
}
