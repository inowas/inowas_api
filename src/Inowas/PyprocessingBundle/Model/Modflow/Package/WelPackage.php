<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class WelPackage implements \JsonSerializable
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
     *  [lay, row, col, flux],
     *  [lay, row, col, flux],
     *  [lay, row, col, flux],
     * ],
     * 1:  [
     *  [lay, row, col, flux],
     *  [lay, row, col, flux],
     *  [lay, row, col, flux],
     * ], ...
     * kper: [
     *  [lay, row, col, flux],
     *  [lay, row, col, flux],
     *  [lay, row, col, flux],
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
     * extension : string
     * Filename extension (default is 'wel')
     *
     * @var string
     */
    private $extension = 'wel';

    /**
     * options : list of strings
     * Package options. (default is None).
     *
     * @var null
     */
    private $options = null;

    /**
     * unitnumber : int
     * File unit number (default is 20).
     *
     * @var int
     */
    private $unitnumber = 20;

    /**
     * @param int $ipakcb
     * @return WelPackage
     */
    public function setIpakcb(int $ipakcb): WelPackage
    {
        $this->ipakcb = $ipakcb;
        return $this;
    }

    /**
     * @param array $stressPeriodData
     * @return WelPackage
     */
    public function setStressPeriodData(array $stressPeriodData): WelPackage
    {
        $this->stressPeriodData = $stressPeriodData;
        return $this;
    }

    /**
     * @param $dtype
     * @return WelPackage
     */
    public function setDtype($dtype): WelPackage
    {
        $this->dtype = $dtype;
        return $this;
    }

    /**
     * @param $options
     * @return WelPackage
     */
    public function setOptions($options): WelPackage
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string $extension
     * @return WelPackage
     */
    public function setExtension(string $extension): WelPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param int $unitnumber
     * @return WelPackage
     */
    public function setUnitnumber(int $unitnumber): WelPackage
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
            'ipakcb' => $this->ipakcb,
            'stress_period_data' => (object)$this->stressPeriodData,
            'dtype' => $this->dtype,
            'options' => $this->options,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}
