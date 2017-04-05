<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

class GhbStressPeriodData implements \JsonSerializable
{
    /** @var null|array */
    protected $data;

    public static function create(): GhbStressPeriodData
    {
        return new self();
    }

    public static function fromArray(array $data): GhbStressPeriodData
    {
        $self = new self();
        $self->data = $data;
        return $self;
    }

    public function addGridCellValue(GhbStressPeriodGridCellValue $gridCellValue): GhbStressPeriodData
    {
        $stressPeriod = $gridCellValue->stressPeriod();
        $layer = $gridCellValue->lay();
        $row = $gridCellValue->row();
        $column = $gridCellValue->col();
        $stage = $gridCellValue->stage();
        $cond = $gridCellValue->cond();

        if (! is_array($this->data)){
            $this->data = array();
        }

        if (! array_key_exists($stressPeriod, $this->data)){
            $this->data[$stressPeriod] = array();
        }

        $this->data[$stressPeriod][] = [$layer, $row, $column, $stage, $cond];
        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return array(
            "stress_period_data" => $this->data
        );
    }
}
