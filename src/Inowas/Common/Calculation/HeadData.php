<?php

declare(strict_types=1);

namespace Inowas\Common\Calculation;

class HeadData implements \JsonSerializable
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

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return $this->data;
    }
}
