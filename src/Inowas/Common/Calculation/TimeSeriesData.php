<?php

declare(strict_types=1);

namespace Inowas\Common\Calculation;

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
