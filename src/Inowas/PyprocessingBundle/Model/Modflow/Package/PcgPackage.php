<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class PcgPackage implements \JsonSerializable
{
    private $mxiter = 50;
    private $iter1 = 30;
    private $npcond = 1;
    private $hclose = 1e-5;
    private $rclose = 1e-5;
    private $relax = 1.0;
    private $nbpol = 0;
    private $iprpcg = 0;
    private $mutpcg = 3;
    private $damp = 1.0;
    private $dampt = 1.0;
    private $ihcofadd = 0;
    private $extension = 'pcg';
    private $unitnumber = 27;

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'mxiter' => $this->mxiter,
            'iter1' => $this->iter1,
            'npcond' => $this->npcond,
            'hclose' => $this->hclose,
            'rclose' => $this->rclose,
            'relax' => $this->relax,
            'nbpol' => $this->nbpol,
            'iprpcg' => $this->iprpcg,
            'mutpcg' => $this->mutpcg,
            'damp' => $this->damp,
            'dampt' => $this->dampt,
            'ihcofadd' => $this->ihcofadd,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}