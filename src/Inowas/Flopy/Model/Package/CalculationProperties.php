<?php

namespace Inowas\FlopyBundle\Model\Package;

class CalculationProperties implements \JsonSerializable
{

    const INITIAL_VALUE_STEADY_STATE_CALCULATION = 'ssc';
    const INITIAL_VALUE_HEAD_FROM_TOP_ELEVATION = 'hft';

    /**
     * Possible arguments are
     * api and nam (for namfile)
     * default is api
     *
     * @var string
     */
    private $loadFrom = 'api';

    /** @var  array */
    private $packages;

    /**
     * $initialValues : Initial Values Parameters
     * Possible Arguments are:
     * 1. 'ssc' for steady state calculation
     * 2. 'hft' head from top elevation in m
     *
     * @var string
     */
    private $initialValues = self::INITIAL_VALUE_STEADY_STATE_CALCULATION;

    /** @var  bool */
    private $check = false;

    /**
     * @var bool
     */
    private $writeInput = true;

    /**
     * @var bool
     */
    private $run = true;

    /**
     * @var bool
     */
    private $submit = false;

    /**
     * @var float
     */
    private $totim = 0.0;

    /**
     * CmdPackage constructor.
     */
    public function __construct() {
        $this->packages = array('mf', 'dis', 'bas', 'lpf', 'pcg', 'oc');
    }

    /**
     * @param string $loadFrom
     * @return CalculationProperties
     */
    public function setLoadFrom(string $loadFrom): CalculationProperties
    {
        $this->loadFrom = $loadFrom;
        return $this;
    }

    /**
     * @param array $packages
     * @return CalculationProperties
     */
    public function setPackages(array $packages): CalculationProperties
    {
        $this->packages = $packages;
        return $this;
    }

    public function addPackage(string $package) : CalculationProperties
    {
        if (! in_array($package, $this->packages) ){
            $this->packages[] = $package;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getInitialValues(): string
    {
        return $this->initialValues;
    }

    /**
     * @param array $initialValues
     * @return CalculationProperties
     */
    public function setInitialValues( $initialValues): CalculationProperties
    {
        $this->initialValues = $initialValues;
        return $this;
    }

    /**
     * @param boolean $check
     * @return CalculationProperties
     */
    public function setCheck(bool $check): CalculationProperties
    {
        $this->check = $check;
        return $this;
    }

    /**
     * @param boolean $writeInput
     * @return CalculationProperties
     */
    public function setWriteInput(bool $writeInput): CalculationProperties
    {
        $this->writeInput = $writeInput;
        return $this;
    }

    /**
     * @param boolean $run
     * @return CalculationProperties
     */
    public function setRun(bool $run): CalculationProperties
    {
        $this->run = $run;
        return $this;
    }

    /**
     * @param $submit
     */
    public function setSubmit(bool $submit)
    {
        $this->submit = $submit;
    }

    /**
     * @param mixed $totim
     */
    public function setTotim($totim = null){
        if (! is_null($totim)){
            $totim = floatval($totim);
        }
        $this->totim = $totim;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(){
        return array(
            'load_from' => $this->loadFrom,
            'packages' => $this->packages,
            'initial_values' => $this->initialValues,
            'check' => $this->check,
            'write_input' => $this->writeInput,
            'run' => $this->run,
            'submit' => $this->submit,
            'totim' => $this->totim
        );
    }

    /**
     * @param array $properties
     * @return CalculationProperties
     */
    public static function fromArray(array $properties){

        $instance = new self();

        if (array_key_exists('load_from', $properties)){
            $instance->setLoadFrom($properties['load_from']);
        }

        if (array_key_exists('packages', $properties)){
            $instance->setPackages($properties['packages']);
        }

        if (array_key_exists('initial_values', $properties)){
            $instance->setInitialValues($properties['initial_values']);
        }

        if (array_key_exists('check', $properties)){
            $instance->setCheck($properties['check']);
        }

        if (array_key_exists('write_input', $properties)){
            $instance->setWriteInput($properties['write_input']);
        }

        if (array_key_exists('run', $properties)){
            $instance->setRun($properties['run']);
        }

        if (array_key_exists('submit', $properties)){
            $instance->setSubmit($properties['submit']);
        }

        if (array_key_exists('totim', $properties)){
            $instance->setTotim($properties['totim']);
        }

        return $instance;
    }
}
