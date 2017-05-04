<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

class WelStressPeriodGridCellValue
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
    protected $flux;

    public static function fromParams(int $sp, int $lay, int $row, int $col, float $flux): WelStressPeriodGridCellValue
    {
        return new self($sp, $lay, $row, $col, $flux);
    }

    private function __construct(int $sp, int $lay, int $row, int $col, float $flux)
    {
        $this->sp = $sp;
        $this->lay = $lay;
        $this->row = $row;
        $this->col = $col;
        $this->flux = $flux;
    }

    public static function fromArray(array $arr): WelStressPeriodGridCellValue
    {
        return new self($arr['sp'], $arr['lay'], $arr['row'], $arr['col'], $arr['flux']);
    }

    public function toArray(): array
    {
        return array(
            'sp' => $this->sp,
            'lay' => $this->lay,
            'row' => $this->row,
            'col' => $this->col,
            'flux' => $this->flux
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

    public function value(): float
    {
        return $this->flux;
    }
}
