<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

class RchStressPeriodData extends AbstractStressPeriodData
{

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

    public function addStressPeriodValue(RchStressPeriodValue $value): RchStressPeriodData
    {
        $stressPeriod = $value->stressPeriod();
        $rech = $value->rech();

        if (! is_array($this->data)){
            $this->data = array();
        }

        if (! array_key_exists($stressPeriod, $this->data)){
            $this->data[$stressPeriod] = array();
        }

        $this->data[$stressPeriod] = $rech->toValue();
        return $this;
    }
}
