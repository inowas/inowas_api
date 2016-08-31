<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;

class BasPackage implements \JsonSerializable
{
    /**
     * ibound : array of ints, optional
     * The ibound array (the default is 1).
     *
     * @var Flopy3DArray $ibound
     */
    private $ibound;

    /**
     * strt : array of floats, optional
     * An array of starting heads (the default is 1.0).
     *
     * @var Flopy2DArray $strt
     */
    private $strt;

    /**
     * ifrefm : bool, optional
     * Indication if data should be read using free format (the default is true)
     *
     * @var bool
     */
    private $ifrefm = true;

    /**
     * ixsec : bool, optional
     * Indication of whether model is cross sectional or not (the default is false).
     *
     * @var bool
     */
    private $ixsec = false;

    /**
     * ichflg : bool, optional
     * Flag indicating that flows between constant head cells should be calculated (the default is false).
     *
     * @var bool
     */
    private $ichflg = false;

    /**
     * stoper : float
     * percent discrepancy that is compared to the budget percent discrepancy
     * continue when the solver convergence criteria are not met.  Execution
     * will unless the budget percent discrepancy is greater than stoper
     * (default is null). MODFLOW-2005 only
     *
     * @var float|null
     */
    private $stoper = null;

    /**
     * hnoflo : float
     * Head value assigned to inactive cells (default is -999.99).
     *
     * @var float
     */
    private $hnoflo = -999.99;

    /**
     * extension : str, optional
     * File extension (default is 'bas').
     *
     * @var string
     */
    private $extension = 'bas';

    /**
     * unitnumber : int, optional
     * FORTRAN unit number for this package (default is 13).
     *
     * @var int
     */
    private $unitnumber = 13;

    /**
     * BasPackage constructor.
     */
    public function __construct()
    {
        $this->ibound = Flopy2DArray::fromValue(1);
        $this->strt = Flopy2DArray::fromValue(1.0);
    }

    /**
     * @param Flopy3DArray $ibound
     * @return BasPackage
     */
    public function setIbound(Flopy3DArray $ibound): BasPackage
    {
        $this->ibound = $ibound;
        return $this;
    }

    /**
     * @param Flopy2DArray $strt
     * @return BasPackage
     */
    public function setStrt(Flopy2DArray $strt): BasPackage
    {
        $this->strt = $strt;
        return $this;
    }

    /**
     * @param boolean $ifrefm
     * @return BasPackage
     */
    public function setIfrefm(bool $ifrefm): BasPackage
    {
        $this->ifrefm = $ifrefm;
        return $this;
    }

    /**
     * @param boolean $ixsec
     * @return BasPackage
     */
    public function setIxsec(bool $ixsec): BasPackage
    {
        $this->ixsec = $ixsec;
        return $this;
    }

    /**
     * @param boolean $ichflg
     * @return BasPackage
     */
    public function setIchflg(bool $ichflg): BasPackage
    {
        $this->ichflg = $ichflg;
        return $this;
    }

    /**
     * @param float|null $stoper
     * @return BasPackage
     */
    public function setStoper($stoper)
    {
        $this->stoper = $stoper;
        return $this;
    }

    /**
     * @param float $hnoflo
     * @return BasPackage
     */
    public function setHnoflo(float $hnoflo): BasPackage
    {
        $this->hnoflo = $hnoflo;
        return $this;
    }

    /**
     * @param string $extension
     * @return BasPackage
     */
    public function setExtension(string $extension): BasPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param int $unitnumber
     * @return BasPackage
     */
    public function setUnitnumber(int $unitnumber): BasPackage
    {
        $this->unitnumber = $unitnumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'ibound' => $this->ibound->toReducedArray(),
            'strt' => $this->strt->toReducedArray(),
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