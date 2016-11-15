<?php

namespace Inowas\ModflowBundle\Model\Package;

class ChdPackage implements \JsonSerializable
{
    /**
     * stress_period_data : list of boundaries, recarrays, or dictionary of
     * boundaries.
     *
     * Each chd cell is defined through definition of
     * layer (int), row (int), column (int), shead (float), ehead (float)
     * shead is the head at the start of the stress period, and ehead is the
     * head at the end of the stress period.
     * The simplest form is a dictionary with a lists of boundaries for each
     * stress period, where each list of boundaries itself is a list of
     * boundaries. Indices of the dictionary are the numbers of the stress
     * period. This gives the form of:
     *
     * stress_period_data =
     * {0: [
     *  [lay, row, col, shead, ehead],
     *  [lay, row, col, shead, ehead],
     *  [lay, row, col, shead, ehead]
     * ],
     * 1: [
     *  [lay, row, col, shead, ehead],
     * [lay, row, col, shead, ehead],
     * [lay, row, col, shead, ehead]
     * ], ...
     * kper:
     * [
     *  [lay, row, col, shead, ehead],
     *  [lay, row, col, shead, ehead],
     *  [lay, row, col, shead, ehead]
     * ]
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
    private $extension = 'chd';

    /**
     * options : list of strings
     * Package options. (default is None).
     *
     * @var null
     */
    private $options = null;

    /**
     * unitnumber : int
     * File unit number (default is 24).
     *
     * @var int
     */
    private $unitnumber = 24;

    /**
     * @param array $stressPeriodData
     * @return ChdPackage
     */
    public function setStressPeriodData(array $stressPeriodData): ChdPackage
    {
        $this->stressPeriodData = $stressPeriodData;
        return $this;
    }

    /**
     * @param null $dtype
     * @return ChdPackage
     */
    public function setDtype($dtype)
    {
        $this->dtype = $dtype;
        return $this;
    }

    /**
     * @param string $extension
     * @return ChdPackage
     */
    public function setExtension(string $extension): ChdPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param null $options
     * @return ChdPackage
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param int $unitnumber
     * @return ChdPackage
     */
    public function setUnitnumber(int $unitnumber): ChdPackage
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
            'stress_period_data' => (object)$this->stressPeriodData,
            'dtype' => $this->dtype,
            'options' => $this->options,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}
