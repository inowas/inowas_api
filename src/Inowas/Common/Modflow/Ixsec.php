<?php
/**
 * ixsec : bool, optional
 * Indication of whether model is cross sectional or not
 * (the default is False).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Ixsec
{
    /** @var bool */
    protected $ixsec;

    public static function fromBool(bool $ixsec): Ixsec
    {
        $self = new self();
        $self->ixsec = $ixsec;
        return $self;
    }

    public static function fromValue($ixsec): Ixsec
    {
        $self = new self();
        $self->ixsec = $ixsec;
        return $self;
    }

    private function __construct(){}

    public function toBool(): bool
    {
        return $this->ixsec;
    }

    public function toValue(): bool
    {
        return $this->ixsec;
    }
}
