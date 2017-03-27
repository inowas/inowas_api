<?php

declare(strict_types=1);

namespace Inowas\Common\Conductivity;

class LayerConductivity
{

    /** @var  LayerKX */
    protected $kx;

    /** @var  LayerKY */
    protected $ky;

    /** @var  LayerKZ */
    protected $kz;

    public static function fromXYZinMPerDay(LayerKX $kx, LayerKY $ky, LayerKZ $kz){
        $self = new self();
        $self->kx = $kx;
        $self->ky = $ky;
        $self->kz = $kz;
        return $self;
    }

    public function kx(): LayerKX
    {
        return $this->kx;
    }

    public function ky(): LayerKY
    {
        return $this->ky;
    }

    public function kz(): LayerKZ
    {
        return $this->kz;
    }

    public function toArray(): array
    {
        return array(
            'kx' => $this->kx->toArray(),
            'ky' => $this->ky->toArray(),
            'kz' => $this->kz->toArray()
        );
    }

    public static function fromArray(array $cond): LayerConductivity
    {
        $self = new self();
        $self->kx = LayerKX::fromArray($cond['kx']);
        $self->ky = LayerKY::fromArray($cond['ky']);
        $self->kz = LayerKZ::fromArray($cond['kz']);
        return $self;
    }
}
