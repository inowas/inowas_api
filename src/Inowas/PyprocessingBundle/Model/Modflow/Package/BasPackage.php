<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class BasPackage implements \JsonSerializable
{
    private $ibound = 1;
    private $strt = 1.0;
    private $ifrefm = true;
    private $ixsec = false;
    private $ichflg = false;
    private $stoper = null;
    private $hnoflo = -999.99;
    private $extension = 'bas';
    private $unitnumber = 13;

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'ibound' => $this->ibound,
            'strt' => $this->strt,
            'ifrefm' => $this->ifrefm,
            'ixsec' => $this->ixsec,
            'ichflg' => $this->ichflg,
            'stoper' => $this->stoper,
            'hnoflo' => $this->hnoflo,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}