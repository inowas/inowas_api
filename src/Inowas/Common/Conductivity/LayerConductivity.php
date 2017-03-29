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

    public function ha(): LayerHA
    {
        $hani = [];
        $kx = $this->kx()->toValue();
        $ky = $this->ky()->toValue();

        if (!is_array($kx) && !is_array($ky)){

            $hani = $kx/$ky;
            return LayerHA::fromValue($hani);

        } elseif (is_array($kx) && !is_array($ky)) {

            foreach ($kx as $iRow => $row) {
                $hani[$iRow] = [];
                foreach ($row as $iCol => $value) {
                    if ($kx[$iRow][$iCol] == 0){
                        $hani[$iRow][$iCol] = 0;
                        continue;
                    }

                    $hani[$iRow][$iCol] = $ky/$kx[$iRow][$iCol];
                }
            }

            return LayerHA::fromArray($hani);
        } elseif (!is_array($kx) && is_array($ky)) {
            foreach ($ky as $iRow => $row) {
                $hani[$iRow] = [];
                foreach ($row as $iCol => $value) {
                    if ($kx == 0){
                        $hani[$iRow][$iCol] = 0;
                        continue;
                    }

                    $hani[$iRow][$iCol] = $ky[$iRow][$iCol]/$kx;
                }
            }

            return LayerHA::fromArray($hani);
        } else {
            foreach ($kx as $iRow => $row) {
                $hani[$iRow] = [];
                foreach ($row as $iCol => $value) {
                    if ($kx[$iRow][$iCol] == 0){
                        $hani[$iRow][$iCol] = 0;
                        continue;
                    }

                    $hani[$iRow][$iCol] = $ky[$iRow][$iCol]/$kx[$iRow][$iCol];
                }
            }

            return LayerHA::fromArray($hani);
        }
    }

    public function toArray(): array
    {
        return array(
            'kx' => $this->kx->toValue(),
            'ky' => $this->ky->toValue(),
            'kz' => $this->kz->toValue()
        );
    }

    public static function fromArray(array $cond): LayerConductivity
    {
        $self = new self();
        $self->kx = LayerKX::fromValue($cond['kx']);
        $self->ky = LayerKY::fromValue($cond['ky']);
        $self->kz = LayerKZ::fromValue($cond['kz']);
        return $self;
    }
}
