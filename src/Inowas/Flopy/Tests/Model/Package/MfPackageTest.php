<?php

namespace Inowas\Flopy\Tests\Model\Package;

use Inowas\Flopy\Model\Package\MfPackage;

class MfPackageTest extends \PHPUnit_Framework_TestCase
{

    protected $modelname;
    protected $exe_name;
    protected $version;
    protected $model_ws;

    public function setUp() {
        $this->modelname = 'MyFancyModel';
        $this->exe_name = 'mf2005';
        $this->version = 'mf2005v';
        $this->model_ws = './ascii';
    }

    public function testInstantiate() {
        $mf = new MfPackage();
        $this->assertInstanceOf(MfPackage::class, $mf);
    }

    public function testDisPackageHasJsonSerializeImplementedAndReturnsJson() {
        $mf = new MfPackage();
        $this->assertJson(json_encode($mf));
    }

    public function testJsonSerialize(){
        $bas = new MfPackage();
        $bas->setModelname($this->modelname);
        $bas->setExeName($this->exe_name);
        $bas->setVersion($this->version);
        $bas->setModelWs($this->model_ws);
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
        $this->assertObjectHasAttribute('load', $mfObj);
        $this->assertObjectHasAttribute('silent', $mfObj);

        $this->assertEquals($this->modelname, $mfObj->modelname);
        $this->assertEquals($this->exe_name, $mfObj->exe_name);
        $this->assertEquals($this->version, $mfObj->version);
        $this->assertEquals($this->model_ws, $mfObj->model_ws);
    }
}
