<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

use Inowas\Common\Modflow\Chani;
use Inowas\Common\Modflow\Hani;
use Inowas\Common\Modflow\Hk;
use Inowas\Common\Modflow\Layvka;
use Inowas\Common\Modflow\Vka;

class Conductivity
{

    /** @var  HydraulicConductivityX */
    protected $hk;

    /** @var  HydraulicConductivityY */
    protected $hky;

    /** @var  HydraulicConductivityZ */
    protected $hkz;

    /** @var  HydraulicAnisotropy */
    protected $hani;

    /** @var VerticalHydraulicConductivity */
    protected $vka;

    public static function fromParams(HydraulicConductivityX $kx, HydraulicAnisotropy $hydraulicAnisotropy, VerticalHydraulicConductivity $verticalHydraulicConductivity){
        $self = new self();
        $self->hk = $kx;
        $self->hani = $hydraulicAnisotropy;
        $self->vka = $verticalHydraulicConductivity;
        $self->hky = HydraulicConductivityY::fromLayerValue(1);
        $self->hkz = HydraulicConductivityZ::fromLayerValue(1);
        return $self;
    }

    public static function fromXYZinMPerDay(HydraulicConductivityX $kx, HydraulicConductivityY $ky, HydraulicConductivityZ $kz): Conductivity
    {
        $self = new self();
        $self->hk = $kx;
        $self->hani = HydraulicAnisotropy::fromLayerValue(1);
        $self->vka = VerticalHydraulicConductivity::fromLayerValue(1);
        $self->hky = $ky;
        $self->hkz = $kz;
        return $self;
    }

    public static function fromArray(array $cond): Conductivity
    {
        $self = new self();
        $self->hk = HydraulicConductivityX::fromArray($cond['hk']);
        $self->hky = HydraulicConductivityY::fromArray($cond['hky']);
        $self->hkz = HydraulicConductivityZ::fromArray($cond['hkz']);
        $self->hani = HydraulicAnisotropy::fromArray($cond['hani']);
        $self->vka = VerticalHydraulicConductivity::fromArray($cond['vka']);
        return $self;
    }

    public static function fromDefault(): Conductivity
    {
        $self = new self();
        $self->hk = HydraulicConductivityX::fromLayerValue(1);
        $self->hani = HydraulicAnisotropy::fromLayerValue(1);
        $self->vka = VerticalHydraulicConductivity::fromLayerValue(1);
        $self->hky = HydraulicConductivityY::fromLayerValue(1);
        $self->hkz = HydraulicConductivityZ::fromLayerValue(1);
        return $self;
    }

    public function hydraulicAnisotropy(): HydraulicAnisotropy
    {
        return $this->hani;
    }

    public function hydraulicConductivityX(): HydraulicConductivityX
    {
        return $this->hk;
    }

    public function kx(): HydraulicConductivityX
    {
        return $this->hk;
    }

    public function ky(): HydraulicConductivityy
    {
        return $this->hky;
    }

    public function kz(): HydraulicConductivityZ
    {
        return $this->hkz;
    }

    public function hk(): Hk
    {
        return Hk::fromValue($this->hk->toValue());
    }

    public function chani(): Chani
    {
        if ($this->hani->isNumeric()){
            return Chani::fromFloat($this->hani->toValue());
        }

        return Chani::fromFloat(0);
    }

    public function hani(): Hani
    {
        if ($this->hani->is2DArray()){
            return Hani::from2DArray($this->hani->toValue());
        }


        return Hani::fromValue(0);
    }

    public function layVka(): Layvka
    {
        // So the VerticalHydraulicConductivity is applied and not a factor
        return Layvka::fromFloat(0);
    }

    public function vka(): Vka
    {
        return Vka::fromValue($this->vka->toValue());
    }

    public function updateHydraulicConductivityX(HydraulicConductivityX $hk): Conductivity
    {
        $self = self::fromArray($this->toArray());
        $self->hk = $hk;
        return $self;
    }

    public function updateHydraulicAnisotropy(HydraulicAnisotropy $hani): Conductivity
    {
        $self = self::fromArray($this->toArray());
        $self->hani = $hani;
        return $self;
    }

    public function updateVerticalHydraulicConductivity(VerticalHydraulicConductivity $vka): Conductivity
    {
        $self = self::fromArray($this->toArray());
        $self->vka = $vka;
        return $self;
    }

    public function verticalHydraulicConductivity(): VerticalHydraulicConductivity
    {
        return $this->vka;
    }

    public function calculateHydraulicAnisotropy(HydraulicConductivityX $kx, HydraulicConductivityY $ky): ?HydraulicAnisotropy
    {

        if ($this->is2DArray($kx->toValue()) && $this->is2DArray($ky->toValue())){
            return HydraulicAnisotropy::fromLayerValue($this->arrayDivision($kx->toValue(), $ky->toValue()));
        }

        if ($this->is2DArray($kx->toValue()) && $this->isNumeric($ky->toValue())){
            $nx = count($kx->toValue()[0]);
            $ny = count($kx->toValue());
            $ky = $this->createArrayFromNumeric($ky->toValue(), $nx, $ny);
            return HydraulicAnisotropy::fromLayerValue($this->arrayDivision($kx->toValue(), $ky));
        }

        if ($kx->isNumeric() && $ky->is2DArray()){
            $nx = count($ky->toValue()[0]);
            $ny = count($ky->toValue());
            $kx = $this->createArrayFromNumeric($kx->toValue(), $nx, $ny);
            return HydraulicAnisotropy::fromLayerValue($this->arrayDivision($kx, $ky->toValue()));
        }

        if ($kx->isNumeric() && $ky->isNumeric()){
            return HydraulicAnisotropy::fromLayerValue($kx/$ky);
        }

        return null;
    }

    public function toArray(): array
    {
        return array(
            'hk' => $this->hk->toArray(),
            'hky' => $this->hky->toArray(),
            'hkz' => $this->hkz->toArray(),
            'hani' => $this->hani->toArray(),
            'vka' => $this->vka->toArray()
        );
    }

    protected function arrayDivision(array $arr1, array $arr2): array
    {
        $result = [];
        if (! $this->sameArrayDimensionAs($arr1, $arr2)) {}

        foreach ($arr1 as $rowNumber => $row) {
        $result[$rowNumber] = [];
            foreach ($arr1 as $colNumber => $value) {
                $result[$rowNumber][$colNumber] = $arr1[$rowNumber][$colNumber]/$arr2[$rowNumber][$colNumber];
            }
        }

        return $result;
    }

    protected function is2DArray($value): bool
    {
        return (is_array($value) && is_array($value[0]) && !is_array($value[0][0]));
    }

    protected function isNumeric($value): bool
    {
        return is_numeric($value);
    }

    protected function createArrayFromNumeric(float $value, int $nx, int $ny): array
    {
        $result = [];
        for($y = 0; $y<$ny; $y++){
            $result[$y] = [];
            for($x = 0; $x<$nx; $x++){
                $result[$y][$x] = $value;
            }
        }

        return $result;
    }

    public function sameArrayDimensionAs($arr1, $arr2): bool
    {
        if ($this->is2DArray($arr1) && $this->is2DArray($arr1)) {
            if (count($arr1) === count($arr2) && count($arr1[0]) === count($arr2[0])){
                return true;
            }
        }

        return false;
    }
}
