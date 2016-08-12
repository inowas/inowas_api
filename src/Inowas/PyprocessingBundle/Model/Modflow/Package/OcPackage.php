<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class OcPackage implements \JsonSerializable
{
    private $ihedfm = 0;
    private $iddnfm = 0;
    private $chedfm = null;
    private $cddnfm = null;
    private $cboufm = null;
    private $compact = null;

    // lets leave this default, so all heads will be saved
    #private $stress_period_data = { (0,0): ['save head'] };

    private $extension = ['oc', 'hds', 'ddn', 'cbc'];
    private $unitnumber = [14, 51, 52, 53];

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'ihedfm' => $this->ihedfm,
            'iddnfm' => $this->iddnfm,
            'chedfm' => $this->chedfm,
            'cddnfm' => $this->cddnfm,
            'cboufm' => $this->cboufm,
            'compact' => $this->compact,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}