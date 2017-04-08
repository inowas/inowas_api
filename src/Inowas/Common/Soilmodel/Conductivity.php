<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class Conductivity
{

    /** @var  HydraulicConductivityX */
    protected $kx;

    /** @var  HydraulicConductivityY */
    protected $ky;

    /** @var  HydraulicConductivityZ */
    protected $kz;

    public static function fromXYZinMPerDay(HydraulicConductivityX $kx, HydraulicConductivityY $ky, HydraulicConductivityZ $kz){
        $self = new self();
        $self->kx = $kx;
        $self->ky = $ky;
        $self->kz = $kz;
        return $self;
    }

    public static function fromDefault(): Conductivity
    {
        $self = new self();
        $self->kx = HydraulicConductivityX::fromLayerValue(1);
        $self->ky = HydraulicConductivityY::fromLayerValue(1);
        $self->kz = HydraulicConductivityZ::fromLayerValue(1);
        return $self;
    }

    public function kx(): HydraulicConductivityX
    {
        return $this->kx;
    }

    public function ky(): HydraulicConductivityY
    {
        return $this->ky;
    }

    public function kz(): HydraulicConductivityZ
    {
        return $this->kz;
    }

    public function ha(): HydraulicAnisotropy
    {
        $hani = [];
        $kx = $this->kx()->toValue();
        $ky = $this->ky()->toValue();

        if (!is_array($kx) && !is_array($ky)){

            $hani = $kx/$ky;
            return HydraulicAnisotropy::fromLayerValue($hani);

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

            return HydraulicAnisotropy::fromLayerValue($hani);
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

            return HydraulicAnisotropy::fromLayerValue($hani);
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

            return HydraulicAnisotropy::fromLayerValue($hani);
        }
    }

    public function toArray(): array
    {
        return array(
            'kx' => $this->kx->toArray(),
            'ky' => $this->ky->toArray(),
            'kz' => $this->kz->toArray()
        );
    }

    public static function fromArray(array $cond): Conductivity
    {
        $self = new self();
        $self->kx = HydraulicConductivityX::fromArray($cond['kx']);
        $self->ky = HydraulicConductivityY::fromArray($cond['ky']);
        $self->kz = HydraulicConductivityZ::fromArray($cond['kz']);
        return $self;
    }
}
