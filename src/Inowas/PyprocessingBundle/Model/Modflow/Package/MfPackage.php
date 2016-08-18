<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

class MfPackage implements \JsonSerializable
{

    private $modelname = 'modflowtest';
    private $namefile_ext = 'nam';
    private $version = 'mf2005';
    private $exe_name = 'mf2005.exe';
    private $structured = true;
    private $listunit = 2;
    private $model_ws = './ascii';
    private $external_path = null;
    private $verbose = false;

    /**
     * MfPackage constructor.
     * @param $modelname
     * @param $exe_name
     * @param $version
     */
    public function __construct($modelname, $exe_name, $version) {
        if ($modelname == ""){
            $modelname = "model_".rand(100000, 200000);
        }
        $this->modelname = $modelname;
        $this->exe_name = $exe_name;
        $this->version = $version;
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
            'verbose' => $this->verbose
        );
    }
}