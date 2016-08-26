<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class CmdPackage implements \JsonSerializable
{
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
     * Returns the 3D-Heads from headsfile
     *
     * if false, no data will be loaded
     * if array the following keys can be passed
     * 'totim' -> total time in days
     *
     * @var bool|array
     */
    private $getData = false;

    /**
     * CmdPackage constructor.
     */
    public function __construct()
    {
        $this->packages = array('mf', 'dis', 'bas', 'lpf', 'pcg', 'oc');
        $this->loadFrom = 'nam';
        $this->getData = array(
            'totim' => 20
        );
    }

    /**
     * @param string $loadFrom
     * @return CmdPackage
     */
    public function setLoadFrom(string $loadFrom): CmdPackage
    {
        $this->loadFrom = $loadFrom;
        return $this;
    }

    /**
     * @param array $packages
     * @return CmdPackage
     */
    public function setPackages(array $packages): CmdPackage
    {
        $this->packages = $packages;
        return $this;
    }

    public function addPackage(string $package) : CmdPackage
    {
        if (! in_array($package, $this->packages) ){
            $this->packages[] = $package;
        }

        return $this;
    }

    /**
     * @param boolean $check
     * @return CmdPackage
     */
    public function setCheck(bool $check): CmdPackage
    {
        $this->check = $check;
        return $this;
    }

    /**
     * @param boolean $writeInput
     * @return CmdPackage
     */
    public function setWriteInput(bool $writeInput): CmdPackage
    {
        $this->writeInput = $writeInput;
        return $this;
    }

    /**
     * @param boolean $run
     * @return CmdPackage
     */
    public function setRun(bool $run): CmdPackage
    {
        $this->run = $run;
        return $this;
    }

    /**
     * @param mixed $getData
     */
    public function setGetData($getData)
    {
        $this->getData = $getData;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'load_from' => $this->loadFrom,
            'packages' => $this->packages,
            'check' => $this->check,
            'write_input' => $this->writeInput,
            'run' => $this->run,
            'get_data' => $this->getData
        );
    }
}