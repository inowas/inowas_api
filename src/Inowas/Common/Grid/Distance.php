<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class Distance
{
    /** @var float */
    protected $meters;

    public static function fromMeters(float $meters): Distance
    {
        return new self($meters);
    }

    private function __construct(float $meters)
    {
        $this->meters = $meters;
    }

    public function inMeters(): float
    {
        return $this->meters;
    }
}
