<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Verbose
{

    protected $verbose;

    public static function fromBool(bool $verbose): Verbose
    {
        $self = new self();
        $self->verbose = $verbose;
        return $self;
    }

    private function __construct(){}

    public function toBool(): bool
    {
        return $this->verbose;
    }
}
