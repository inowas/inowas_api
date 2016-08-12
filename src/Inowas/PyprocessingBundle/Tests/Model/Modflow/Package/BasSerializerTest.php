<?php

namespace Inowas\PyprocessingBundle\Tests\Model\Modflow\Package;

use Inowas\PyprocessingBundle\Model\Modflow\Package\MfPackage;

class ModflowPackageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MfPackage
     */
    protected $basSerializer;

    /** @var  string */
    protected $modelname;

    /** @var  string */
    protected $exe_name;

    /** @var  string */
    protected $version;

    /** @var  string */
    protected $model_ws;

    public function setUp(){
        $this->modelname = 'MyFancyModel';
        $this->exe_name = 'mf2005';
        $this->version = 'mf2005v';
        $this->model_ws = '.';
    }

    public function testInstantiate(){
        $bas = new MfPackage($this->modelname, $this->exe_name, $this->version, $this->model_ws);
        $this->assertInstanceOf(MfPackage::class, $bas);
    }

    public function testJsonSerialize(){
        $bas = new MfPackage($this->modelname, $this->exe_name, $this->version, $this->model_ws);
        $json = json_encode($bas);
        $this->assertJson($json);
        $mfObj = json_decode($json);
        $this->assertObjectHasAttribute('modelname', $mfObj);
        $this->assertObjectHasAttribute('namefile_ext', $mfObj);
        $this->assertObjectHasAttribute('version', $mfObj);
        $this->assertObjectHasAttribute('exe_name', $mfObj);
        $this->assertObjectHasAttribute('structured', $mfObj);
        $this->assertObjectHasAttribute('listunit', $mfObj);
        $this->assertObjectHasAttribute('model_ws', $mfObj);
        $this->assertObjectHasAttribute('external_path', $mfObj);
        $this->assertObjectHasAttribute('verbose', $mfObj);

        $this->assertEquals($this->modelname, $mfObj->modelname);
        $this->assertEquals($this->exe_name, $mfObj->exe_name);
        $this->assertEquals($this->version, $mfObj->version);
        $this->assertEquals($this->model_ws, $mfObj->model_ws);
    }
}
