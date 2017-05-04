<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

class GhbStressPeriodGridCellValue
{
    /** @var  int */
    protected $sp;

    /** @var  int */
    protected $lay;

    /** @var  int */
    protected $row;

    /** @var  int */
    protected $col;

    /** @var  float */
    protected $stage;

    /** @var  float */
    protected $cond;

    public static function fromParams(int $sp, int $lay, int $row, int $col, float $stage, float $cond): GhbStressPeriodGridCellValue
    {
        return new self($sp, $lay, $row, $col, $stage, $cond);
    }

    private function __construct(int $sp, int $lay, int $row, int $col, float $stage, float $cond)
    {
        $this->sp = $sp;
        $this->lay = $lay;
        $this->row = $row;
        $this->col = $col;
        $this->stage = $stage;
        $this->cond = $cond;
    }

    public static function fromArray(array $arr): GhbStressPeriodGridCellValue
    {
        return new self($arr['sp'], $arr['lay'], $arr['row'], $arr['col'], $arr['stage'], $arr['cond']);
    }

    public function toArray(): array
    {
        return array(
            'sp' => $this->sp,
            'lay' => $this->lay,
            'row' => $this->row,
            'col' => $this->col,
            'stage' => $this->stage,
            'cond' => $this->cond
        );
    }

    public function stressPeriod(): int
    {
        return $this->sp;
    }

    public function lay(): int
    {
        return $this->lay;
    }

    public function row(): int
    {
        return $this->row;
    }

    public function col(): int
    {
        return $this->col;
    }

    public function stage(): float
    {
        return $this->stage;
    }

    public function cond(): float
    {
        return $this->cond;
    }
}
