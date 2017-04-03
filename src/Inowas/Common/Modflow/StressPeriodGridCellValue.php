<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class StressPeriodGridCellValue
{
    /** @var  int */
    protected $sp;
    protected $lay;
    protected $row;
    protected $col;
    protected $value;

    public static function fromParams(int $sp, int $lay, int $row, int $col, float $value): StressPeriodGridCellValue
    {
        return new self($sp, $lay, $row, $col, $value);
    }

    private function __construct(int $sp, int $lay, int $row, int $col, float $value)
    {
        $this->sp = $sp;
        $this->lay = $lay;
        $this->row = $row;
        $this->col = $col;
        $this->value = $value;
    }

    public static function fromArray(array $arr): StressPeriodGridCellValue
    {
        return new self($arr['sp'], $arr['lay'], $arr['row'], $arr['col'], $arr['value']);
    }

    public function toArray(): array
    {
        return array(
            'sp' => $this->sp,
            'lay' => $this->lay,
            'row' => $this->row,
            'col' => $this->col,
            'value' => $this->value
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
        return $this->value;
    }
}
