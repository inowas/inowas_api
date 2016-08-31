<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class GhbPackage implements \JsonSerializable
{
    /**
     * ipakcb : int
     * A flag that is used to determine if cell-by-cell budget data should be
     * saved. If ipakcb is non-zero cell-by-cell budget data will be saved.
     * (default is 0).
     */
    private $ipakcb = 0;

    /**
     * stress_period_data : list of boundaries, recarray of boundaries or,
     * dictionary of boundaries.
     *
     * Each ghb cell is defined through definition of
     * layer(int), row(int), column(int), stage(float), conductance(float)
     * The simplest form is a dictionary with a lists of boundaries for each
     * stress period, where each list of boundaries itself is a list of
     * boundaries. Indices of the dictionary are the numbers of the stress
     * period. This gives the form of:
     *
     * stress_period_data =
     * {0: [
     *  [lay, row, col, stage, cond],
     *  [lay, row, col, stage, cond],
     *  [lay, row, col, stage, cond],
     * ],
     * 1:  [
     *  [lay, row, col, stage, cond],
     *  [lay, row, col, stage, cond],
     *  [lay, row, col, stage, cond],
     * ], ...
     * kper:
     * [
     *  [lay, row, col, stage, cond],
     *  [lay, row, col, stage, cond],
     *  [lay, row, col, stage, cond],
     * ]
     * }
     *
     * Note that if no values are specified for a certain stress period, then
     * the list of boundaries for the previous stress period for which values
     * were defined is used. Full details of all options to specify
     * stress_period_data can be found in the flopy3boundaries Notebook in
     * the basic subdirectory of the examples directory
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
     * Filename extension (default is 'ghb')
     *
     * @var string
     */
    private $extension = 'ghb';

    /**
     * options : list of strings
     * Package options. (default is None).
     *
     * @var null
     */
    private $options = null;

    /**
     * unitnumber : int
     * File unit number (default is 23).
     *
     * @var int
     */
    private $unitnumber = 23;

    /**
     * @param mixed $ipakcb
     * @return GhbPackage
     */
    public function setIpakcb($ipakcb)
    {
        $this->ipakcb = $ipakcb;
        return $this;
    }

    /**
     * @param array $stressPeriodData
     * @return GhbPackage
     */
    public function setStressPeriodData(array $stressPeriodData): GhbPackage
    {
        $this->stressPeriodData = $stressPeriodData;
        return $this;
    }

    /**
     * @param null $dtype
     * @return GhbPackage
     */
    public function setDtype($dtype)
    {
        $this->dtype = $dtype;
        return $this;
    }

    /**
     * @param string $extension
     * @return GhbPackage
     */
    public function setExtension(string $extension): GhbPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param null $options
     * @return GhbPackage
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param int $unitnumber
     * @return GhbPackage
     */
    public function setUnitnumber(int $unitnumber): GhbPackage
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