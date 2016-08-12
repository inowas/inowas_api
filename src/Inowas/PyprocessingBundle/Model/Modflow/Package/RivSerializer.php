<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\StreamBoundary;

class RivSerializer
{

    /** @var  StreamBoundary $riv */
    protected $riv;

    public function __construct(StreamBoundary $riv)
    {
        $this->riv = $riv;
    }

    public function serialize(){

        $stressPeriodData = array();

        $rcond = 1050;
        $rbot = -4;
        $layer = 0;
        $stage = 12.1;

        $activeCells = $this->riv->getActiveCells()->toArray();

        for ($iy = 0; $iy < count($activeCells); $iy++) {
            for ($ix = 0; $ix < count($activeCells[0]); $ix++) {
                if ($activeCells[$iy][$ix] == true) {
                    $stressPeriodData[0][] = array($layer, $iy, $ix, $stage, $rcond, $rbot);
                }
            }
        }

        $data = array(
            'ipakcb' => 0,
            'stress_period_data' => $stressPeriodData,
            'dtype' => null,
            'extension' => 'riv',
            'unitnumber' => 18,
            'options' => null
        );

        return json_encode($data);
    }
}