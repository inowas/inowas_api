<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

use Inowas\Common\DateTime\Stressperiod;

class ModflowModelStressperiods
{
    /** @var array */
    protected $stressPeriods = [];

    public function addStressPeriod(Stressperiod $stressperiod): ModflowModelStressperiods
    {
        $this->stressPeriods[] = $stressperiod;
        return $this;
    }

    public function getStressPeriods(): array
    {
        return $this->stressPeriods;
    }

    public function countUniqueTotims(): int
    {
        return count($this->uniqueTotalTimes());
    }

    public function count(): int
    {
        return count($this->stressPeriods);
    }

    public function perlen(): array
    {
        $perlen=[];
        $totims = $this->uniqueTotalTimes();
        usort($totims, function($a, $b) {
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        $count = count($totims);
        for ($i = 1; $i < $count; $i++){
            $perlen[] = ($totims[$i] - $totims[$i-1]);
        }

        return $perlen;
    }

    public function steady(): array
    {
        $steady = [];
        $sps = $this->sortedUniqueStressperiodsByTotim();
        /** @var Stressperiod $sp */
        foreach ($sps as $sp){
            $steady[] = $sp->steady()->toValue();
        }

        return $steady;
    }

    public function nstp(): array
    {
        $nstp = [];
        $sps = $this->sortedUniqueStressperiodsByTotim();
        /** @var Stressperiod $sp */
        foreach ($sps as $sp){
            $nstp[] = $sp->nstp()->toValue();
        }

        return $nstp;
    }

    public function tsMult(): array
    {
        $tsMult = [];
        $sps = $this->sortedUniqueStressperiodsByTotim();
        /** @var Stressperiod $sp */
        foreach ($sps as $sp){
            $tsMult[] = $sp->tsMult()->toValue();
        }

        return $tsMult;
    }

    private function uniqueTotalTimes(): array
    {
        $totims = [];
        $sps = $this->sortedUniqueStressperiodsByTotim();
        foreach ($sps as $sp){
            $totims[] = $sp->totalTime()->toInteger();
        }

        return $totims;
    }

    private function sortedUniqueStressperiodsByTotim(): array
    {
        $uniqueSps = [];
        /** @var Stressperiod $stressPeriod */
        foreach ($this->stressPeriods as $stressPeriod) {
            $uniqueSps[$stressPeriod->totalTime()->toInteger()] = $stressPeriod;
        }

        ksort($uniqueSps);

        $result = [];
        foreach ($uniqueSps as $sp){
            $result[] = $sp;
        }

        return $result;
    }
}
