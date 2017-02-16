<?php

namespace Inowas\Modflow\Model;

class CalculationResultData
{
    /** @var  array */
    private $data;

    public static function from3dArray(array $data): CalculationResultData
    {
        $self = new self();
        $self->data = $data;
        return $self;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
