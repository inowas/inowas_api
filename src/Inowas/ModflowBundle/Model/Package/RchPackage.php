<?php

namespace Inowas\ModflowBundle\Model\Package;

use Inowas\ModflowBundle\Model\ValueObject\Flopy2DArray;

class RchPackage implements \JsonSerializable
{
    /**
     * ipakcb : int
     * A flag that is used to determine if cell-by-cell budget data should be
     * saved. If ipakcb is non-zero cell-by-cell budget data will be saved.
     * (default is 0).
     *
     * @var int
     */
    private $ipakcb = 0;

    /**
     * nrchop : int
     * is the recharge option code.
     * 1: Recharge to top grid layer only
     * 2: Recharge to layer defined in irch
     * 3: Recharge to highest active cell (default is 3).
     *
     * @var int
     */
    private $nrchop = 3;

    /**
     * rech : float or array of floats (nrow, ncol)
     * is the recharge flux. (default is 1.e-3).
     *
     * @var Flopy2DArray
     */
    private $rech;

    /**
     * irch : int or array of ints (nrow, ncol)
     * is the layer to which recharge is applied in each vertical
     * column (only used when nrchop=2). (default is 0).
     *
     * @var int
     */
    private $irch = 0;

    /**
     * extension : string
     * Filename extension (default is 'wel')
     *
     * @var string
     */
    private $extension = 'rch';

    /**
     * unitnumber : int
     * File unit number (default is 19).
     *
     * @var int
     */
    private $unitnumber = 19;

    /**
     * @param int $ipakcb
     * @return RchPackage
     */
    public function setIpakcb(int $ipakcb): RchPackage
    {
        $this->ipakcb = $ipakcb;
        return $this;
    }

    /**
     * @param int $nrchop
     * @return RchPackage
     */
    public function setNrchop(int $nrchop): RchPackage
    {
        $this->nrchop = $nrchop;
        return $this;
    }

    /**
     * @param array $rech
     * @return RchPackage
     */
    public function setRech(array $rech): RchPackage
    {
        $this->rech = $rech;
        return $this;
    }

    /**
     * @param int $irch
     * @return RchPackage
     */
    public function setIrch(int $irch): RchPackage
    {
        $this->irch = $irch;
        return $this;
    }

    /**
     * @param string $extension
     * @return RchPackage
     */
    public function setExtension(string $extension): RchPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param int $unitnumber
     * @return RchPackage
     */
    public function setUnitnumber(int $unitnumber): RchPackage
    {
        $this->unitnumber = $unitnumber;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'nrchop' => $this->nrchop,
            'ipakcb' => $this->ipakcb,
            'rech' => (object)$this->rech,
            'irch' => $this->irch,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}
