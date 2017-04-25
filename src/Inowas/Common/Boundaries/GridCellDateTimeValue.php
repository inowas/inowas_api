<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class GridCellDateTimeValue
{
    private $layer;
    private $row;
    private $column;
    private $dateTimeValue;

    public static function fromParams(int $layer, int $row, int $column, DateTimeValue $dateTimeValue): GridCellDateTimeValue
    {
        return new self($layer, $row, $column, $dateTimeValue);
    }

    private function __construct(int $layer, int $row, int $column, DateTimeValue $dateTimeValue)
    {
        $this->layer = $layer;
        $this->row = $row;
        $this->column = $column;
        $this->dateTimeValue = $dateTimeValue;
    }

    public function layer(): int
    {
        return $this->layer;
    }

    public function row(): int
    {
        return $this->row;
    }

    public function column(): int
    {
        return $this->column;
    }

    public function dateTimeValue(): DateTimeValue
    {
        return $this->dateTimeValue;
    }
}
