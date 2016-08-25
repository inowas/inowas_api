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
     * CmdPackage constructor.
     */
    public function __construct()
    {
        $this->packages = array('mf', 'dis', 'bas', 'lpf', 'pcg', 'oc');
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
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'loadFrom' => $this->loadFrom,
            'packages' => $this->packages,
            'check' => $this->check,
            'writeInput' => $this->writeInput,
            'run' => $this->run
        );
    }
}