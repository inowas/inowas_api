<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class DisPackage implements \JsonSerializable
{

    private $nlay = 1;
    private $nrow = 2;
    private $ncol = 2;
    private $nper = 1;
    private $delr = 1.0;
    private $delc = 1.0;
    private $laycbd = 0;
    private $top = 1;
    private $botm = 0;
    private $perlen = 1;
    private $nstp = 1;
    private $tsmult = 1;
    private $steady = true;
    private $itmuni = 4;
    private $lenuni = 2;
    private $extension = 'dis';
    private $unitnumber = 11;
    private $xul = null;
    private $yul = null;
    private $rotation = 0.0;
    private $proj4_str = null;
    private $start_datetime = null;

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
     * @param float $delr
     * @return DisPackage
     */
    public function setDelr(float $delr): DisPackage
    {
        $this->delr = $delr;
        return $this;
    }

    /**
     * @param float $delc
     * @return DisPackage
     */
    public function setDelc(float $delc): DisPackage
    {
        $this->delc = $delc;
        return $this;
    }

    /**
     * @param int $laycbd
     * @return DisPackage
     */
    public function setLaycbd(int $laycbd): DisPackage
    {
        $this->laycbd = $laycbd;
        return $this;
    }

    /**
     * @param int $top
     * @return DisPackage
     */
    public function setTop(int $top): DisPackage
    {
        $this->top = $top;
        return $this;
    }

    /**
     * @param int $botm
     * @return DisPackage
     */
    public function setBotm(int $botm): DisPackage
    {
        $this->botm = $botm;
        return $this;
    }

    /**
     * @param int $perlen
     * @return DisPackage
     */
    public function setPerlen(int $perlen): DisPackage
    {
        $this->perlen = $perlen;
        return $this;
    }

    /**
     * @param int $nstp
     * @return DisPackage
     */
    public function setNstp(int $nstp): DisPackage
    {
        $this->nstp = $nstp;
        return $this;
    }

    /**
     * @param int $tsmult
     * @return DisPackage
     */
    public function setTsmult(int $tsmult): DisPackage
    {
        $this->tsmult = $tsmult;
        return $this;
    }

    /**
     * @param boolean $steady
     * @return DisPackage
     */
    public function setSteady(bool $steady): DisPackage
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
     * @param null $proj4_str
     * @return DisPackage
     */
    public function setProj4Str($proj4_str)
    {
        $this->proj4_str = $proj4_str;
        return $this;
    }

    /**
     * @param null $start_datetime
     * @return DisPackage
     */
    public function setStartDatetime($start_datetime)
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
            'delr' => $this->delr,
            'delc' => $this->delc,
            'laycbd' => $this->laycbd,
            'top' => $this->top,
            'botm' => $this->botm,
            'perlen' => $this->perlen,
            'nstp' => $this->nstp,
            'tsmult' => $this->tsmult,
            'steady' => $this->steady,
            'itmuni' => $this->itmuni,
            'lenuni' => $this->lenuni,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber,
            'xul' => $this->xul,
            'yul' => $this->yul,
            'rotation' => $this->rotation,
            'proj4_str' => $this->proj4_str,
            'start_datetime' => $this->start_datetime
        );
    }
}