<?php

namespace Inowas\FlopyBundle\Model\Package;

class MfPackage implements PackageInterface
{

    /**
     * modelname : string, optional
     * Name of model.  This string will be used to name the MODFLOW input
     * that are created with write_model. (the default is 'modflowtest')
     *
     * @var string
     */
    private $modelname = 'modflowtest';

    /**
     * namefile_ext : string, optional
     * Extension for the namefile (the default is 'nam')
     *
     * @var string
     */
    private $namefile_ext = 'nam';

    /**
     * version : string, optional
     * Version of MODFLOW to use (the default is 'mf2005').
     *
     * @var string
     */
    private $version = 'mf2005';

    /**
     * exe_name : string, optional
     * The name of the executable to use (the default is 'mf2005.exe').
     *
     * @var string
     */
    private $exe_name = 'mf2005.exe';

    /**
     * structured : bool, optional
     * The definition was not found in the flopy documentation (the default is true)
     *
     * @var bool
     */
    private $structured = true;


    /**
     * listunit : integer, optional
     * Unit number for the list file (the default is 2).
     *
     * @var int
     */
    private $listunit = 2;

    /**
     * @var string
     *
     * model_ws : string, optional
     * model workspace.  Directory name to create model data sets
     * (default is the present working directory).
     */
    private $model_ws = '.';

    /**
     * external_path : string
     * Location for external files (default is null).
     *
     * @var null
     */
    private $external_path = null;

    /**
     * verbose : boolean, optional
     * Print additional information to the screen (default is False).
     *
     * @var bool
     */
    private $verbose = false;

    /**
     * load : boolean, optional
     * (default is True).
     *
     * @var bool
     */
    private $load = true;

    /**
     * silent : integer
     * (default is 0)
     *
     * @var integer
     */
    private $silent = 0;

    /**
     * MfPackage constructor.
     */
    public function __construct() {}

    /**
     * @param string $modelname
     * @return MfPackage
     */
    public function setModelname(string $modelname): MfPackage
    {
        $this->modelname = $modelname;
        return $this;
    }

    /**
     * @param string $namefile_ext
     * @return MfPackage
     */
    public function setNamefileExt(string $namefile_ext): MfPackage
    {
        $this->namefile_ext = $namefile_ext;
        return $this;
    }

    /**
     * @param string $version
     * @return MfPackage
     */
    public function setVersion(string $version): MfPackage
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @param string $exe_name
     * @return MfPackage
     */
    public function setExeName(string $exe_name): MfPackage
    {
        $this->exe_name = $exe_name;
        return $this;
    }

    /**
     * @param boolean $structured
     * @return MfPackage
     */
    public function setStructured(bool $structured): MfPackage
    {
        $this->structured = $structured;
        return $this;
    }

    /**
     * @param int $listunit
     * @return MfPackage
     */
    public function setListunit(int $listunit): MfPackage
    {
        $this->listunit = $listunit;
        return $this;
    }

    /**
     * @param string $model_ws
     * @return MfPackage
     */
    public function setModelWs(string $model_ws): MfPackage
    {
        $this->model_ws = $model_ws;
        return $this;
    }

    /**
     * @param null $external_path
     * @return MfPackage
     */
    public function setExternalPath($external_path)
    {
        $this->external_path = $external_path;
        return $this;
    }

    /**
     * @param boolean $verbose
     * @return MfPackage
     */
    public function setVerbose(bool $verbose): MfPackage
    {
        $this->verbose = $verbose;
        return $this;
    }

    /**
     * @param boolean $load
     * @return MfPackage
     */
    public function setLoad(bool $load): MfPackage
    {
        $this->load = $load;
        return $this;
    }

    /**
     * @param int $silent
     * @return MfPackage
     */
    public function setSilent(int $silent): MfPackage
    {
        $this->silent = $silent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'modelname' => $this->modelname,
            'namefile_ext' => $this->namefile_ext,
            'version' => $this->version,
            'exe_name' => $this->exe_name,
            'structured' => $this->structured,
            'listunit' => $this->listunit,
            'model_ws' => $this->model_ws,
            'external_path' => $this->external_path,
            'verbose' => $this->verbose,
            'load' => $this->load,
            'silent' => $this->silent
        );
    }
}
