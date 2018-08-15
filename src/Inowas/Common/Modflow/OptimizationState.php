<?php

namespace Inowas\Common\Modflow;


class OptimizationState
{
    public const NEW = 0;
    public const STARTED = 1;
    public const CALCULATING = 2;
    public const FINISHED = 3;
    public const CANCELLING = 11;
    public const CANCELLED = 12;

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
