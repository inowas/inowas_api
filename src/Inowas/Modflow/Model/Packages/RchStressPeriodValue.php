<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\Rech;

class RchStressPeriodValue
{
    /** @var  int */
    protected $sp;

    /** @var  Rech */
    protected $rech;

    public static function fromParams(int $sp, Rech $rech): RchStressPeriodValue
    {
        return new self($sp, $rech);
    }

    private function __construct(int $sp, Rech $rech)
    {
        $this->sp = $sp;
        $this->rech = $rech;
    }

    public static function fromArray(array $arr): RchStressPeriodValue
    {
        return new self($arr['sp'], $arr['rech']);
    }

    public function toArray(): array
    {
        return array(
            'sp' => $this->sp,
            'rech' => $this->rech
        );
    }

    public function stressPeriod(): int
    {
        return $this->sp;
    }

    public function rech(): Rech
    {
        return $this->rech;
    }
}
