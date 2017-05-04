<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class GridCellDateTimeValues
{
    /** @var int  */
    private $layer;

    /** @var int  */
    private $row;

    /** @var int  */
    private $column;

    /** @var DateTimeValue[] */
    private $dateTimeValues;

    public static function fromParams(int $layer, int $row, int $column, array $dateTimeValues): GridCellDateTimeValues
    {
        return new self($layer, $row, $column, $dateTimeValues);
    }

    private function __construct(int $layer, int $row, int $column, array $dateTimeValues)
    {
        $this->layer = $layer;
        $this->row = $row;
        $this->column = $column;
        $this->dateTimeValues = $dateTimeValues;
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

    public function dateTimeValues(): array
    {
        return $this->dateTimeValues;
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): ?DateTimeValue
    {
        $values = $this->dateTimeValues();
        usort($values, function ($v1, $v2) {

            /** @var $v1 DateTimeValue */
            $dtV1 = $v1->dateTime();

            /** @var $v2 DateTimeValue */
            $dtV2 = $v2->dateTime();

            return ($dtV1 < $dtV2) ? +1 : -1;
        });

        /** @var DateTimeValue $value */
        foreach ($values as $value) {
            if ($dateTime >= $value->dateTime()){
                return $value;
            }
        }

        return null;
    }
}
