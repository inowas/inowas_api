<?php

declare(strict_types=1);

namespace Inowas\Common\Conductivity;

class Conductivity
{

    /** @var  KX */
    protected $kx;

    /** @var  KY */
    protected $ky;

    /** @var  KZ */
    protected $kz;

    public static function fromXYZinMPerDay(KX $kx, KY $ky, KZ $kz){
        $self = new self();
        $self->kx = $kx;
        $self->ky = $ky;
        $self->kz = $kz;
        return $self;
    }

    public function kx(): KX
    {
        return $this->kx;
    }

    public function ky(): KY
    {
        return $this->ky;
    }

    public function kz(): KZ
    {
        return $this->kz;
    }

    public function toArray(): array
    {
        return array(
            'kx' => $this->kx->mPerDay(),
            'ky' => $this->ky->mPerDay(),
            'kz' => $this->kz->mPerDay()
        );
    }

    public static function fromArray(array $cond): Conductivity
    {
        $self = new self();
        $self->kx = KX::fromMPerDay($cond['kx']);
        $self->ky = KY::fromMPerDay($cond['ky']);
        $self->kz = KZ::fromMPerDay($cond['kz']);
        return $self;
    }
}
