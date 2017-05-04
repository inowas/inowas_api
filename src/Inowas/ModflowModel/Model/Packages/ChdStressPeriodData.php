<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

class ChdStressPeriodData extends AbstractStressPeriodData
{
    /** @var array */
    protected $data = [];

    public static function create(): ChdStressPeriodData
    {
        return new self();
    }

    public static function fromArray(array $data): ChdStressPeriodData
    {
        $self = new self();
        $self->data = $data;
        return $self;
    }

    public function addGridCellValue(ChdStressPeriodGridCellValue $gridCellValue): ChdStressPeriodData
    {
        $stressPeriod = $gridCellValue->stressPeriod();
        $layer = $gridCellValue->lay();
        $row = $gridCellValue->row();
        $column = $gridCellValue->col();
        $shead = $gridCellValue->shead();
        $ehead = $gridCellValue->ehead();

        if (! is_array($this->data)){
            $this->data = array();
        }

        if (! array_key_exists($stressPeriod, $this->data)){
            $this->data[$stressPeriod] = array();
        }

        $this->data[$stressPeriod][] = [$layer, $row, $column, $shead, $ehead];
        return $this;
    }
}
