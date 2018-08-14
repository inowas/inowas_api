<?php

namespace Inowas\Common\Modflow;


class OptimizationState
{
    public const STARTED_BY_USER = 1;
    public const CALCULATING = 2;
    public const FINISHED = 2;
    public const CANCELED_BY_USER = 11;
    public const STOPPED = 12;

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
