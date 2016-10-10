<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy1DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;

class DisPackage implements \JsonSerializable
{

    /**
     * NLAY
     * is the number of layers in the model grid.
     * In MODFLOW-2000, the maximum number of layers is 999.
     * In MODFLOW-2005, there is no fixed limit to the number of layers.
     *
     * @var int
     */
    private $nlay = 1;

    /**
     * NROW
     * is the number of rows in the model grid.
     *
     * @var int
     */
    private $nrow = 2;

    /**
     * NCOL
     * is the number of columns in the model grid.
     *
     * @var int
     */
    private $ncol = 2;

    /**
     * NPER
     * is the number of stress periods in the simulation.
     *
     * @var int
     */
    private $nper = 1;

    /**
     * DELR
     * is the cell width along rows. Read one value for each of the NCOL columns.
     * This is a multi-value one-dimensional variable with one value for each column.
     *
     * @var Flopy1DArray
     */
    private $delr;

    /**
     * DELC
     * is the cell width along columns. Read one value for each of the NROW rows.
     * This is a multi-value one-dimensional variable with one value for each row.
     *
     * @var Flopy1DArray
     */
    private $delc;

    /**
     * LAYCBD
     * is a flag, with one value for each model layer,
     * that indicates whether or not a layer has a Quasi-3D confining bed below it.
     * 0 indicates no confining bed, and not zero indicates a confining bed.
     * LAYCBD for the bottom layer must be 0.
     *
     * @var Flopy1DArray
     */
    private $laycbd;

    /**
     * TOP
     * is the top elevation of layer 1.
     * For the common situation in which the top layer represents a water-table aquifer,
     * it may be reasonable to set Top equal to land-surface elevation.
     *
     * @var Flopy2DArray
     */
    private $top;

    /**
     * BOTM
     * is the bottom elevation of a model layer or a Quasi-3d confining bed.
     *
     * @var Flopy3DArray
     */
    private $botm;

    /**
     * PERLEN
     * is the length of a stress period.
     *
     * @var Flopy1DArray
     */
    private $perlen;

    /**
     * NSTP
     * is the number of time steps in a stress period
     *
     * @var Flopy1DArray
     */
    private $nstp;

    /**
     * TSMULT
     * is the multiplier for the length of successive time steps.
     * The length of a time step is calculated by multiplying the length of the previous time step by TSMULT.
     * The length of the first time step, Dt1, is related to PERLEN, NSTP, and TSMULT by the relation
     *
     * @var Flopy1DArray
     */
    private $tsmult;

    /**
     * STEADY
     * boolean if stressPeriod is steadyState
     *
     * @var Flopy1DArray
     */
    private $steady;

    /**
     * ITMUNI
     * indicates the time unit of model data, which must be consistent for all data values that involve time.
     * For example, if years is the chosen time unit,
     * stress-period length, time-step length, transmissivity, and so on,
     * must all be expressed using years for their time units.
     * Note that the program will still run even if “undefined” time units are specified
     * because the fundamental equations used in MODFLOW do not require that the time unit be identified.
     * But be sure to use consistent units for all input data even when ITMUNI indicates an undefined time unit.
     * When the time unit is defined, MODFLOW uses it to print a table of elapsed simulation time:
     *
     * 0 - undefined
     * 1 - seconds
     * 2 - minutes
     * 3 - hours
     * 4 - days
     * 5 - years
     *
     * @var int
     */
    private $itmuni = 4;

    /**
     * LENUNI
     * indicates the length unit of model data, which must be consistent for all data values that involve length.
     * For example, if feet is the chosen length unit, grid spacing, head, hydraulic conductivity,
     * water volumes, and so forth, must all be expressed using feet for their length units.
     * Note that the program will still run even if “undefined” length units are specified because
     * the fundamental equations used in MODFLOW do not require that the length unit be identified.
     * But be sure to use consistent units for all input data even when LENUNI indicates an undefined length unit:
     *
     * 0 - undefined
     * 1 - feet
     * 2 - meters
     * 3 - centimeters
     *
     * @var int
     */
    private $lenuni = 2;

    /**
     * Filename extension (default is 'dis')
     *
     * @var string
     */
    private $extension = 'dis';

    /**
     * File unit number (default is 11).
     *
     * @var int
     */
    private $unitnumber = 11;

    /**
     * xul : float
     * x coordinate of upper left corner of the grid, default is null
     *
     * @var float|null
     */
    private $xul = null;

    /**
     * yul : float
     * y coordinate of upper left corner of the grid, default is null
     *
     * @var float|null
     */
    private $yul = null;

    /**
     * rotation : float
     * clockwise rotation (in degrees) of the grid about the upper left
     * corner. default is 0.0
     *
     * @var float
     */
    private $rotation = 0.0;

    /**
     * proj4_str : str
     * PROJ4 string that defines the xul-yul coordinate system
     * (.e.g. '+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs ').
     * Can be an EPSG code (e.g. 'EPSG:4326'). Default is 'EPSG:4326'
     *
     * @var string
     */
    private $proj4_str = null;

    /**
     * start_dateteim : str
     * starting datetime of the simulation. default is '1/1/1970'
     *
     * @var \DateTimeImmutable|null
     */
    private $start_datetime = null;

    /**
     * DisPackage constructor.
     */
    public function __construct(){
        $this->delr = Flopy1DArray::fromNumeric(1.0, $this->nrow);
        $this->delc = Flopy1DArray::fromNumeric(1.0, $this->ncol);
        $this->laycbd = Flopy1DArray::fromNumeric(0, $this->nlay);
        $this->top = Flopy2DArray::fromNumeric(1, $this->nrow, $this->ncol);
        $this->botm = Flopy3DArray::fromNumeric(0, $this->nlay, $this->nrow, $this->ncol);
        $this->perlen = Flopy1DArray::fromNumeric(1, $this->nper);
        $this->nstp = Flopy1DArray::fromNumeric(1, $this->nper);
        $this->tsmult = Flopy1DArray::fromNumeric(1, $this->nper);
        $this->steady = Flopy1DArray::fromBool(true, $this->nper);
        $this->start_datetime = new \DateTimeImmutable('1/1/1970');
    }

    /**
     * @param int $nlay
     * @return DisPackage
     */
    public function setNlay(int $nlay): DisPackage
    {
        $this->nlay = $nlay;
        return $this;
    }

    /**
     * @param int $nrow
     * @return DisPackage
     */
    public function setNrow(int $nrow): DisPackage
    {
        $this->nrow = $nrow;
        return $this;
    }

    /**
     * @param int $ncol
     * @return DisPackage
     */
    public function setNcol(int $ncol): DisPackage
    {
        $this->ncol = $ncol;
        return $this;
    }

    /**
     * @param int $nper
     * @return DisPackage
     */
    public function setNper(int $nper): DisPackage
    {
        $this->nper = $nper;
        return $this;
    }

    /**
     * @param Flopy1DArray $delr
     * @return DisPackage
     */
    public function setDelr(Flopy1DArray $delr): DisPackage
    {
        $this->delr = $delr;
        return $this;
    }

    /**
     * @param Flopy1DArray $delc
     * @return DisPackage
     */
    public function setDelc(Flopy1DArray $delc): DisPackage
    {
        $this->delc = $delc;
        return $this;
    }

    /**
     * @param Flopy1DArray $laycbd
     * @return DisPackage
     */
    public function setLaycbd(Flopy1DArray $laycbd): DisPackage
    {
        $this->laycbd = $laycbd;
        return $this;
    }

    /**
     * @param Flopy2DArray $top
     * @return DisPackage
     */
    public function setTop(Flopy2DArray $top): DisPackage
    {
        $this->top = $top;
        return $this;
    }

    /**
     * @param Flopy3DArray $botm
     * @return DisPackage
     */
    public function setBotm(Flopy3DArray $botm): DisPackage
    {
        $this->botm = $botm;
        return $this;
    }

    /**
     * @param Flopy1DArray $perlen
     * @return DisPackage
     */
    public function setPerlen(Flopy1DArray $perlen): DisPackage
    {
        $this->perlen = $perlen;
        return $this;
    }

    /**
     * @param Flopy1DArray $nstp
     * @return DisPackage
     */
    public function setNstp(Flopy1DArray $nstp): DisPackage
    {
        $this->nstp = $nstp;
        return $this;
    }

    /**
     * @param Flopy1DArray $tsmult
     * @return DisPackage
     */
    public function setTsmult(Flopy1DArray $tsmult): DisPackage
    {
        $this->tsmult = $tsmult;
        return $this;
    }

    /**
     * @param Flopy1DArray $steady
     * @return DisPackage
     */
    public function setSteady(Flopy1DArray $steady): DisPackage
    {
        $this->steady = $steady;
        return $this;
    }

    /**
     * @param int $itmuni
     * @return DisPackage
     */
    public function setItmuni(int $itmuni): DisPackage
    {
        $this->itmuni = $itmuni;
        return $this;
    }

    /**
     * @param int $lenuni
     * @return DisPackage
     */
    public function setLenuni(int $lenuni): DisPackage
    {
        $this->lenuni = $lenuni;
        return $this;
    }

    /**
     * @param string $extension
     * @return DisPackage
     */
    public function setExtension(string $extension): DisPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param int $unitnumber
     * @return DisPackage
     */
    public function setUnitnumber(int $unitnumber): DisPackage
    {
        $this->unitnumber = $unitnumber;
        return $this;
    }

    /**
     * @param null $xul
     * @return DisPackage
     */
    public function setXul($xul)
    {
        $this->xul = $xul;
        return $this;
    }

    /**
     * @param null $yul
     * @return DisPackage
     */
    public function setYul($yul)
    {
        $this->yul = $yul;
        return $this;
    }

    /**
     * @param float $rotation
     * @return DisPackage
     */
    public function setRotation(float $rotation): DisPackage
    {
        $this->rotation = $rotation;
        return $this;
    }

    /**
     * @param string $proj4_str
     * @return $this
     */
    public function setProj4Str(string $proj4_str)
    {
        $this->proj4_str = $proj4_str;
        return $this;
    }

    /**
     * @param \DateTimeImmutable $start_datetime
     * @return $this
     */
    public function setStartDatetime(\DateTimeImmutable $start_datetime)
    {
        $this->start_datetime = $start_datetime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'nlay' => $this->nlay,
            'nrow' => $this->nrow,
            'ncol' => $this->ncol,
            'nper' => $this->nper,
            'delr' => $this->delr->toSingleNumericValueOrFullArray(),
            'delc' => $this->delc->toSingleNumericValueOrFullArray(),
            'laycbd' => $this->laycbd->toSingleNumericValueOrFullArray(),
            'top' => $this->top->toSingleNumericValueOrFullArray(),
            'botm' => $this->botm->toSingleNumericValueOrFullArray(),
            'perlen' => $this->perlen->toSingleNumericValueOrFullArray(),
            'nstp' => $this->nstp->toSingleNumericValueOrFullArray(),
            'tsmult' => $this->tsmult->toSingleNumericValueOrFullArray(),
            'steady' => $this->steady->toSingleNumericValueOrFullArray(),
            'itmuni' => $this->itmuni,
            'lenuni' => $this->lenuni,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber,
            'xul' => $this->xul,
            'yul' => $this->yul,
            'rotation' => $this->rotation,
            'proj4_str' => $this->proj4_str,
            'start_datetime' => $this->start_datetime->format('m/d/Y')
        );
    }
}
