<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class LpfPackage implements \JsonSerializable
{

    private $laytyp = 0;
    private $layavg = 0;
    private $chani = 1.0;
    private $layvka = 0;
    private $laywet = 0;
    private $ipakcb = 53;
    private $hdry = -1E+30;
    private $iwdflg = 0;
    private $wetfct = 0.1;
    private $iwetit = 1;
    private $ihdwet = 0;
    private $hk = 1.0;
    private $hani = 1.0;
    private $vka = 1.0;
    private $ss = 1e-5;
    private $sy = 0.15;
    private $vkcb = 0.0;
    private $wetdry = -0.01;
    private $storagecoefficient = false;
    private $constantcv = false;
    private $thickstrt = false;
    private $nocvcorrection = false;
    private $novfc = false;
    private $extension = 'lpf';
    private $unitnumber = 15;


    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'laytyp' => $this->laytyp,
            'layavg' => $this->layavg,
            'chani' => $this->chani,
            'layvka' => $this->layvka,
            'laywet' => $this->laywet,
            'ipakcb' => $this->ipakcb,
            'hdry' => $this->hdry,
            'iwdflg' => $this->iwdflg,
            'wetfct' => $this->wetfct,
            'iwetit' => $this->iwetit,
            'ihdwet' => $this->ihdwet,
            'hk' => $this->hk,
            'hani' => $this->hani,
            'vka' => $this->vka,
            'ss' => $this->ss,
            'sy' => $this->sy,
            'vkcb' => $this->vkcb,
            'wetdry' => $this->wetdry,
            'storagecoefficient' => $this->storagecoefficient,
            'constantcv' => $this->constantcv,
            'thickstrt' => $this->thickstrt,
            'nocvcorrection' => $this->nocvcorrection,
            'novfc' => $this->novfc,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}