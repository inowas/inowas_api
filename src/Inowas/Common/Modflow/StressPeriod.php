<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

final class StressPeriod
{
    /** @var int  */
    private $totimStart;

    /** @var int  */
    private $perlen;

    /** @var int  */
    private $nstp;

    /** @var float  */
    private $tsmult;

    /** @var bool  */
    private $steady;

    public static function create(int $totimStart, int $perlen, int $nstp, float $tsmult, bool $steady){
        return new self($totimStart, $perlen, $nstp, $tsmult, $steady);
    }

    private function __construct(int $totimStart, int $perlen, int $nstp, float $tsmult, bool $steady){
        $this->totimStart = $totimStart;
        $this->perlen = $perlen;
        $this->nstp = $nstp;
        $this->tsmult = $tsmult;
        $this->steady = $steady;
    }

    public function totimStart(): int
    {
        return $this->totimStart;
    }

    public function perlen(): int
    {
        return $this->perlen;
    }

    public function nstp(): int
    {
        return $this->nstp;
    }

    public function tsmult(): float
    {
        return $this->tsmult;
    }

    public function steady(): bool
    {
        return $this->steady;
    }
}
