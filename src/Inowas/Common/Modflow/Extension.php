<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;


class Extension
{

    protected $extension;

    public static function fromString(string $extension): Extension
    {
        $self = new self();
        $self->extension = $extension;
        return $self;
    }

    public static function fromValue($extension): Extension
    {
        $self = new self();
        $self->extension = $extension;
        return $self;
    }

    private function __construct(){}

    public function toString(): string
    {
        return $this->extension;
    }

    public function toValue(): string
    {
        return $this->extension;
    }
}
