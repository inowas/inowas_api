<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;


class GeologicalLayerType
{

    const TYPE_CONFINED = 0;
    const TYPE_CONVERTIBLE = 1;
    const TYPE_CONVERTIBLE_UNLESS_THICKSTRT_OPTION_IS_IN_EFFECT = -1;

    /** @var  int */
    private $type;

    private function __construct()
    {}

    public static function fromValue(int $value): GeologicalLayerType
    {
        $self = new self();
        $self->type = $value;
        return $self;
    }

    public function toValue(): int
    {
        return $this->type;
    }

    public function toInt(): int
    {
        return $this->type;
    }
}
