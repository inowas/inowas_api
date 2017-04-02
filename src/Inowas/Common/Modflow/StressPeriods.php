<?php

namespace Inowas\Common\Modflow;


final class StressPeriods
{

    private $stressperiods = [];

    public static function create(){
        return new self();
    }

    private function __construct(){}

    public function addStressPeriod(StressPeriod $stressPeriod): void
    {
        $this->stressperiods[] = $stressPeriod;
    }

    public function perlen(): Perlen
    {
        $arr = [];
        /** @var StressPeriod $stressperiod */
        foreach ($this->stressperiods as $stressperiod) {
            $arr[] = $stressperiod->perlen();
        }

        return Perlen::fromArray($arr);
    }

    public function nstp(): Nstp
    {
        $arr = [];
        /** @var StressPeriod $stressperiod */
        foreach ($this->stressperiods as $stressperiod) {
            $arr[] = $stressperiod->nstp();
        }

        return Nstp::fromArray($arr);
    }

    public function tsmult(): Tsmult
    {
        $arr = [];
        /** @var StressPeriod $stressperiod */
        foreach ($this->stressperiods as $stressperiod) {
            $arr[] = $stressperiod->tsmult();
        }

        return Tsmult::fromArray($arr);
    }

    public function steady(): Steady
    {
        $arr = [];
        /** @var StressPeriod $stressperiod */
        foreach ($this->stressperiods as $stressperiod) {
            $arr[] = $stressperiod->steady();
        }

        return Steady::fromArray($arr);
    }

    public function nper(): Nper
    {
        return Nper::fromInteger(count($this->stressperiods));
    }
}
