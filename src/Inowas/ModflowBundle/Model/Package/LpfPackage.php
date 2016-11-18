<?php

namespace Inowas\ModflowBundle\Model\Package;

use Inowas\ModflowBundle\Model\ValueObject\Flopy1DArray;
use Inowas\ModflowBundle\Model\ValueObject\Flopy3DArray;

class LpfPackage implements \JsonSerializable
{
    /**
     * laytyp : int or array of ints (nlay)
     * Layer type (default is 0).
     *
     * @var Flopy1DArray
     */
    private $laytyp;

    /**
     * layavg : int or array of ints (nlay)
     * Layer average (default is 0).
     * 0 is harmonic mean
     * 1 is logarithmic mean
     * 2 is arithmetic mean of saturated thickness and logarithmic mean of
     * of hydraulic conductivity
     *
     * @var Flopy1DArray
     */
    private $layavg;

    /**
     * chani : float or array of floats (nlay)
     * contains a value for each layer that is a flag or the horizontal
     * anisotropy. If CHANI is less than or equal to 0, then variable HANI
     * defines horizontal anisotropy. If CHANI is greater than 0, then CHANI
     * is the horizontal anisotropy for the entire layer, and HANI is not
     * read. If any HANI parameters are used, CHANI for all layers must be
     * less than or equal to 0. Use as many records as needed to enter a
     * value of CHANI for each layer. The horizontal anisotropy is the ratio
     * of the hydraulic conductivity along columns (the Y direction) to the
     * hydraulic conductivity along rows (the X direction).
     *
     * @var Flopy1DArray
     */
    private $chani;

    /**
     * layvka : float or array of floats (nlay)
     * a flag for each layer that indicates whether variable VKA is vertical
     * hydraulic conductivity or the ratio of horizontal to vertical
     * hydraulic conductivity.
     *
     * @var Flopy1DArray
     */
    private $layvka;

    /**
     * laywet : float or array of floats (nlay)
     * contains a flag for each layer that indicates if wetting is active.
     *
     * @var Flopy1DArray
     */
    private $laywet;

    /**
     * ipakcb : int
     * A flag that is used to determine if cell-by-cell budget data should be
     * saved. If ipakcb is non-zero cell-by-cell budget data will be saved.
     * (default is 53)
     *
     * @var int
     */
    private $ipakcb = 53;

    /**
     * hdry : float
     * Is the head that is assigned to cells that are converted to dry during
     * a simulation. Although this value plays no role in the model
     * calculations, it is useful as an indicator when looking at the
     * resulting heads that are output from the model. HDRY is thus similar
     * to HNOFLO in the Basic Package, which is the value assigned to cells
     * that are no-flow cells at the start of a model simulation. (default
     * is -1.e30).
     *
     * @var float
     */
    private $hdry = -1E+30;

    /**
     * iwdflg : int
     * Not documented (default is 0).
     *
     * @var int
     */
    private $iwdflg = 0;

    /**
     * wetfct : float
     * is a factor that is included in the calculation of the head that is
     * initially established at a cell when it is converted from dry to wet.
     * (default is 0.1).
     *
     * @var float
     */
    private $wetfct = 0.1;

    /**
     * iwetit : int
     * is the iteration interval for attempting to wet cells. Wetting is
     * attempted every IWETIT iteration. If using the PCG solver
     * (Hill, 1990), this applies to outer iterations, not inner iterations.
     * If IWETIT  less than or equal to 0, it is changed to 1.
     * (default is 1).
     * @var int
     */
    private $iwetit = 1;

    /**
     * ihdwet : int
     * is a flag that determines which equation is used to define the
     * initial head at cells that become wet. (default is 0)
     *
     * @var int
     */
    private $ihdwet = 0;

    /**
     * hk : float or array of floats (nlay, nrow, ncol)
     * is the hydraulic conductivity along rows. HK is multiplied by
     * horizontal anisotropy (see CHANI and HANI) to obtain hydraulic
     * conductivity along columns. (default is 1.0).
     *
     * @var Flopy3DArray
     */
    private $hk;

    /**
     * hani : float or array of floats (nlay, nrow, ncol)
     * is the ratio of hydraulic conductivity along columns to hydraulic
     * conductivity along rows, where HK of item 10 specifies the hydraulic
     * conductivity along rows. Thus, the hydraulic conductivity along
     * columns is the product of the values in HK and HANI.
     * (default is 1.0).
     *
     * @var Flopy3DArray
     */
    private $hani;

    /**
     * vka : float or array of floats (nlay, nrow, ncol)
     * is either vertical hydraulic conductivity or the ratio of horizontal
     * to vertical hydraulic conductivity depending on the value of LAYVKA.
     * (default is 1.0).
     *
     * @var Flopy3DArray
     */
    private $vka;

    /**
     * ss : float or array of floats (nlay, nrow, ncol)
     * is specific storage unless the STORAGECOEFFICIENT option is used.
     * When STORAGECOEFFICIENT is used, Ss is confined storage coefficient.
     * (default is 1.e-5).
     *
     * @var Flopy3DArray
     */
    private $ss;

    /**
     * sy : float or array of floats (nlay, nrow, ncol)
     * is specific yield. (default is 0.15).
     *
     * @var Flopy3DArray
     */
    private $sy;

    /**
     * vkcb : float or array of floats (nlay, nrow, ncol)
     * is the vertical hydraulic conductivity of a Quasi-three-dimensional
     * confining bed below a layer. (default is 0.0).
     *
     * @var Flopy3DArray
     */
    private $vkcb;

    /**
     * wetdry : float or array of floats (nlay, nrow, ncol)
     * is a combination of the wetting threshold and a flag to indicate
     * which neighboring cells can cause a cell to become wet.
     * (default is -0.01).
     *
     * @var Flopy3DArray
     */
    private $wetdry;

    /**
     * storagecoefficient : boolean
     * indicates that variable Ss and SS parameters are read as storage
     * coefficient rather than specific storage. (default is False).
     *
     * @var bool
     */
    private $storagecoefficient = false;

    /**
     * constantcv : boolean
     * indicates that vertical conductance for an unconfined cell is
     * computed from the cell thickness rather than the saturated thickness.
     * The CONSTANTCV option automatically invokes the NOCVCORRECTION
     * option. (default is False).
     *
     * @var bool
     */
    private $constantcv = false;

    /**
     * thickstrt : boolean
     * indicates that layers having a negative LAYTYP are confined, and their
     * cell thickness for conductance calculations will be computed as
     * STRT-BOT rather than TOP-BOT. (default is False).
     *
     * @var bool
     */
    private $thickstrt = false;

    /**
     * nocvcorrection : boolean
     * indicates that vertical conductance is not corrected when the vertical
     * flow correction is applied. (default is False).
     *
     * @var bool
     */
    private $nocvcorrection = false;

    /**
     * novfc : boolean
     * turns off the vertical flow correction under dewatered conditions.
     * This option turns off the vertical flow calculation described on p.
     * 5-8 of USGS Techniques and Methods Report 6-A16 and the vertical
     * conductance correction described on p. 5-18 of that report.
     * (default is False).
     *
     * @var bool
     */
    private $novfc = false;

    /**
     * extension : string
     * Filename extension (default is 'lpf')
     *
     * @var string
     */
    private $extension = 'lpf';

    /**
     * unitnumber : int
     * File unit number (default is 15).
     *
     * @var int
     */
    private $unitnumber = 15;

    /**
     * LpfPackage constructor.
     */
    public function __construct(){
        $this->laytyp = Flopy1DArray::fromValue(0);
        $this->layavg = Flopy1DArray::fromValue(0);
        $this->chani = Flopy1DArray::fromValue(0);
        $this->layvka = Flopy1DArray::fromValue(0.0);
        $this->laywet = Flopy1DArray::fromValue(0.0);
        $this->hk = Flopy3DArray::fromValue(1.0);
        $this->hani = Flopy3DArray::fromValue(1.0);
        $this->vka = Flopy3DArray::fromValue(1.0);
        $this->ss = Flopy3DArray::fromValue(1e-5);
        $this->sy = Flopy3DArray::fromValue(0.15);
        $this->vkcb = Flopy3DArray::fromValue(0.0);
        $this->wetdry = Flopy3DArray::fromValue(-0.01);
    }

    /**
     * @param Flopy1DArray $laytyp
     * @return LpfPackage
     */
    public function setLaytyp(Flopy1DArray $laytyp): LpfPackage
    {
        $this->laytyp = $laytyp;
        return $this;
    }

    /**
     * @param Flopy1DArray $layavg
     * @return LpfPackage
     */
    public function setLayavg(Flopy1DArray $layavg): LpfPackage
    {
        $this->layavg = $layavg;
        return $this;
    }

    /**
     * @param Flopy1DArray $chani
     * @return LpfPackage
     */
    public function setChani(Flopy1DArray $chani): LpfPackage
    {
        $this->chani = $chani;
        return $this;
    }

    /**
     * @param Flopy1DArray $layvka
     * @return LpfPackage
     */
    public function setLayvka(Flopy1DArray $layvka): LpfPackage
    {
        $this->layvka = $layvka;
        return $this;
    }

    /**
     * @param Flopy1DArray $laywet
     * @return LpfPackage
     */
    public function setLaywet(Flopy1DArray $laywet): LpfPackage
    {
        $this->laywet = $laywet;
        return $this;
    }

    /**
     * @param int $ipakcb
     * @return LpfPackage
     */
    public function setIpakcb(int $ipakcb): LpfPackage
    {
        $this->ipakcb = $ipakcb;
        return $this;
    }

    /**
     * @param float $hdry
     * @return LpfPackage
     */
    public function setHdry(float $hdry): LpfPackage
    {
        $this->hdry = $hdry;
        return $this;
    }

    /**
     * @param int $iwdflg
     * @return LpfPackage
     */
    public function setIwdflg(int $iwdflg): LpfPackage
    {
        $this->iwdflg = $iwdflg;
        return $this;
    }

    /**
     * @param float $wetfct
     * @return LpfPackage
     */
    public function setWetfct(float $wetfct): LpfPackage
    {
        $this->wetfct = $wetfct;
        return $this;
    }

    /**
     * @param int $iwetit
     * @return LpfPackage
     */
    public function setIwetit(int $iwetit): LpfPackage
    {
        $this->iwetit = $iwetit;
        return $this;
    }

    /**
     * @param int $ihdwet
     * @return LpfPackage
     */
    public function setIhdwet(int $ihdwet): LpfPackage
    {
        $this->ihdwet = $ihdwet;
        return $this;
    }

    /**
     * @param Flopy3DArray $hk
     * @return LpfPackage
     */
    public function setHk(Flopy3DArray $hk): LpfPackage
    {
        $this->hk = $hk;
        return $this;
    }

    /**
     * @param Flopy3DArray $hani
     * @return LpfPackage
     */
    public function setHani(Flopy3DArray $hani): LpfPackage
    {
        $this->hani = $hani;
        return $this;
    }

    /**
     * @param Flopy3DArray $vka
     * @return LpfPackage
     */
    public function setVka(Flopy3DArray $vka): LpfPackage
    {
        $this->vka = $vka;
        return $this;
    }

    /**
     * @param Flopy3DArray $ss
     * @return LpfPackage
     */
    public function setSs(Flopy3DArray $ss): LpfPackage
    {
        $this->ss = $ss;
        return $this;
    }

    /**
     * @param Flopy3DArray $sy
     * @return LpfPackage
     */
    public function setSy(Flopy3DArray $sy): LpfPackage
    {
        $this->sy = $sy;
        return $this;
    }

    /**
     * @param Flopy3DArray $vkcb
     * @return LpfPackage
     */
    public function setVkcb(Flopy3DArray $vkcb): LpfPackage
    {
        $this->vkcb = $vkcb;
        return $this;
    }

    /**
     * @param Flopy3DArray $wetdry
     * @return LpfPackage
     */
    public function setWetdry(Flopy3DArray $wetdry): LpfPackage
    {
        $this->wetdry = $wetdry;
        return $this;
    }

    /**
     * @param boolean $storagecoefficient
     * @return LpfPackage
     */
    public function setStoragecoefficient(bool $storagecoefficient): LpfPackage
    {
        $this->storagecoefficient = $storagecoefficient;
        return $this;
    }

    /**
     * @param boolean $constantcv
     * @return LpfPackage
     */
    public function setConstantcv(bool $constantcv): LpfPackage
    {
        $this->constantcv = $constantcv;
        return $this;
    }

    /**
     * @param boolean $thickstrt
     * @return LpfPackage
     */
    public function setThickstrt(bool $thickstrt): LpfPackage
    {
        $this->thickstrt = $thickstrt;
        return $this;
    }

    /**
     * @param boolean $nocvcorrection
     * @return LpfPackage
     */
    public function setNocvcorrection(bool $nocvcorrection): LpfPackage
    {
        $this->nocvcorrection = $nocvcorrection;
        return $this;
    }

    /**
     * @param boolean $novfc
     * @return LpfPackage
     */
    public function setNovfc(bool $novfc): LpfPackage
    {
        $this->novfc = $novfc;
        return $this;
    }

    /**
     * @param string $extension
     * @return LpfPackage
     */
    public function setExtension(string $extension): LpfPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param int $unitnumber
     * @return LpfPackage
     */
    public function setUnitnumber(int $unitnumber): LpfPackage
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
            'laytyp' => $this->laytyp->toSingleNumericValueOrFullArray(),
            'layavg' => $this->layavg->toSingleNumericValueOrFullArray(),
            'chani' => $this->chani->toSingleNumericValueOrFullArray(),
            'layvka' => $this->layvka->toSingleNumericValueOrFullArray(),
            'laywet' => $this->laywet->toSingleNumericValueOrFullArray(),
            'ipakcb' => $this->ipakcb,
            'hdry' => $this->hdry,
            'iwdflg' => $this->iwdflg,
            'wetfct' => $this->wetfct,
            'iwetit' => $this->iwetit,
            'ihdwet' => $this->ihdwet,
            'hk' => $this->hk->toSingleNumericValueOrFullArray(),
            'hani' => $this->hani->toSingleNumericValueOrFullArray(),
            'vka' => $this->vka->toSingleNumericValueOrFullArray(),
            'ss' => $this->ss->toSingleNumericValueOrFullArray(),
            'sy' => $this->sy->toSingleNumericValueOrFullArray(),
            'vkcb' => $this->vkcb->toSingleNumericValueOrFullArray(),
            'wetdry' => $this->wetdry->toSingleNumericValueOrFullArray(),
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
