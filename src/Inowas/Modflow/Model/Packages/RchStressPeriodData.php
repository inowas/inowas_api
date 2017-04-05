<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

class RchStressPeriodData implements \JsonSerializable
{
    /** @var null|array */
    protected $data;

    public static function create(): RchStressPeriodData
    {
        return new self();
    }

    public static function fromArray(array $data): RchStressPeriodData
    {
        $self = new self();
        $self->data = $data;
        return $self;
    }

    public function addGridCellValue(RchStressPeriodValue $value): RchStressPeriodData
    {
        $stressPeriod = $value->stressPeriod();
        $rech = $value->rech();

        if (! is_array($this->data)){
            $this->data = array();
        }

        if (! array_key_exists($stressPeriod, $this->data)){
            $this->data[$stressPeriod] = array();
        }

        $this->data[$stressPeriod] = $rech;
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
