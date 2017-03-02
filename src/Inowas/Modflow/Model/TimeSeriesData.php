<?php

namespace Inowas\Modflow\Model;

class TimeSeriesData
{
    /** @var  array */
    private $data;

    public static function fromArray(array $data): TimeSeriesData
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
