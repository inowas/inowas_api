<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class RivPackage implements \JsonSerializable
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
     * stress_period_data : list of boundaries, or recarray of boundaries, or
     * dictionary of boundaries.
     * Each river cell is defined through definition of
     * layer (int), row (int), column (int), stage (float), cond (float),
     * rbot (float).
     * The simplest form is a dictionary with a lists of boundaries for each
     * stress period, where each list of boundaries itself is a list of
     * boundaries. Indices of the dictionary are the numbers of the stress
     * period. This gives the form of:
     *
     * stress_period_data =
     * {0: [
     *  [lay, row, col, stage, cond, rbot],
     *  [lay, row, col, stage, cond, rbot],
     *  [lay, row, col, stage, cond, rbot]
     * ],
     * 1:  [
     *  [lay, row, col, stage, cond, rbot],
     *  [lay, row, col, stage, cond, rbot],
     *  [lay, row, col, stage, cond, rbot]
     * ], ...
     * kper: [
     *  [lay, row, col, stage, cond, rbot],
     *  [lay, row, col, stage, cond, rbot],
     *  [lay, row, col, stage, cond, rbot]
     * ]}
     *
     * Note that if the number of lists is smaller than the number of stress
     * periods, then the last list of rivers will apply until the end of the
     * simulation. Full details of all options to specify stress_period_data
     * can be found in the flopy3 boundaries Notebook in the basic
     * subdirectory of the examples directory.
     *
     * @var array
     */
    private $stressPeriodData;

    /**
     * dtype : custom datatype of stress_period_data.
     * If None the default river datatype will be applied.
     * (default is None)
     *
     * @var null
     */
    private $dtype = null;

    /**
     * options : list of strings
     * Package options. (default is None).
     *
     * @var null
     */
    private $options = null;

    /**
     * naux : int
     * number of auxiliary variables
     *
     * @var int|null
     */
    private $naux = null;

    /**
     * extension : string
     * Filename extension (default is 'riv')
     *
     * @var string
     */
    private $extension = 'riv';

    /**
     * unitnumber : int
     * File unit number (default is 18).
     *
     * @var int
     */
    private $unitnumber = 18;

    /**
     * @return int
     */
    public function getIpakcb(): int
    {
        return $this->ipakcb;
    }

    /**
     * @return array
     */
    public function getStressPeriodData(): array
    {
        return $this->stressPeriodData;
    }

    /**
     * @return null
     */
    public function getDtype()
    {
        return $this->dtype;
    }

    /**
     * @return null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return int|null
     */
    public function getNaux()
    {
        return $this->naux;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return $this->unitnumber;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'ipakcb' => $this->ipakcb,
            'stress_period_data' => $this->stressPeriodData,
            'dtype' => $this->dtype,
            'options' => $this->options,
            'naux' => $this->naux,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}