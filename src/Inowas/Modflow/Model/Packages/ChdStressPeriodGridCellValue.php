<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

class ChdStressPeriodGridCellValue
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
    protected $shead;

    /** @var  float */
    protected $ehead;

    public static function fromParams(int $sp, int $lay, int $row, int $col, float $shead, float $ehead): ChdStressPeriodGridCellValue
    {
        return new self($sp, $lay, $row, $col, $shead, $ehead);
    }

    private function __construct(int $sp, int $lay, int $row, int $col, float $shead, float $ehead)
    {
        $this->sp = $sp;
        $this->lay = $lay;
        $this->row = $row;
        $this->col = $col;
        $this->shead = $shead;
        $this->ehead = $ehead;
    }

    public static function fromArray(array $arr): ChdStressPeriodGridCellValue
    {
        return new self($arr['sp'], $arr['lay'], $arr['row'], $arr['col'], $arr['shead'], $arr['ehead']);
    }

    public function toArray(): array
    {
        return array(
            'sp' => $this->sp,
            'lay' => $this->lay,
            'row' => $this->row,
            'col' => $this->col,
            'shead' => $this->shead,
            'ehead' => $this->ehead
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

    public function shead(): float
    {
        return $this->shead;
    }

    public function ehead(): float
    {
        return $this->ehead;
    }
}
