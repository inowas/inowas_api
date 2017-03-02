<?php

namespace Inowas\Modflow\Model;

class HeadData
{
    /** @var  array */
    private $data;

    public static function from2dArray(array $data): HeadData
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
