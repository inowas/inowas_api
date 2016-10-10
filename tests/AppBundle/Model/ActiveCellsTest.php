<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\ActiveCells;

class ActiveCellsTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateFromArray(){
        $data = array(array(1,2,3));
        $activeCells = ActiveCells::fromArray($data);
        $this->assertInstanceOf(ActiveCells::class, $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testCreateFromArrayWithSingleFieldsSet(){
        $data[2][2] = true;
        $activeCells = ActiveCells::fromArray($data);
        $this->assertInstanceOf(ActiveCells::class, $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testCreateFromObject(){
        $data = array("2" => array(2=>1, 3=>2, 5=>3));
        $activeCells = ActiveCells::fromObject((object)$data);
        $this->assertInstanceOf(ActiveCells::class, $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testCreateFromBiggerObject(){
        $data = json_decode('{"12":{"74":true},"13":{"71":true,"72":true,"73":true,"74":true},"14":{"58":true,"59":true,"60":true,"70":true,"71":true},"15":{"51":true,"52":true,"53":true,"58":true,"60":true,"61":true,"69":true,"70":true},"16":{"50":true,"51":true,"53":true,"54":true,"58":true,"61":true,"68":true,"69":true},"17":{"49":true,"50":true,"54":true,"55":true,"58":true,"61":true,"65":true,"66":true,"67":true,"68":true},"18":{"49":true,"55":true,"56":true,"57":true,"58":true,"61":true,"62":true,"63":true,"64":true,"65":true},"19":{"49":true,"50":true},"20":{"30":true,"31":true,"32":true,"33":true,"34":true,"35":true,"36":true,"37":true,"50":true,"51":true},"21":{"29":true,"30":true,"37":true,"38":true,"51":true,"52":true},"22":{"28":true,"29":true,"38":true,"52":true},"23":{"27":true,"28":true,"38":true,"51":true,"52":true},"24":{"26":true,"27":true,"38":true,"39":true,"40":true,"50":true,"51":true},"25":{"24":true,"25":true,"26":true,"40":true,"41":true,"50":true},"26":{"22":true,"23":true,"24":true,"41":true,"49":true,"50":true},"27":{"21":true,"22":true,"41":true,"48":true,"49":true},"28":{"17":true,"18":true,"19":true,"20":true,"21":true,"41":true,"42":true,"43":true,"44":true,"45":true,"46":true,"47":true,"48":true},"29":{"16":true,"17":true,"43":true,"44":true},"30":{"16":true},"31":{"16":true},"32":{"16":true},"33":{"16":true},"34":{"15":true,"16":true},"35":{"14":true,"15":true},"36":{"13":true,"14":true},"37":{"9":true,"10":true,"11":true,"12":true,"13":true},"38":{"8":true,"9":true,"11":true,"12":true},"39":{"6":true,"7":true,"8":true}}');
        $activeCells = ActiveCells::fromObject((object)$data);
        $this->assertInstanceOf(ActiveCells::class, $activeCells);
        $this->assertEquals(true, $activeCells->toArray()[12][74]);
        $this->assertEquals(true, $activeCells->toArray()[13][71]);
        $this->assertEquals(true, $activeCells->toArray()[13][72]);
        $this->assertEquals(true, $activeCells->toArray()[13][73]);
        $this->assertEquals(true, $activeCells->toArray()[13][74]);
    }

    public function testCreateFromJsonArray(){
        $data = array("2" => array(2=>1, 3=>2, 5=>3));
        $activeCells = ActiveCells::fromJSON(json_encode($data));
        $this->assertInstanceOf(ActiveCells::class, $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testCreateFromJsonObject(){
        $data = array("2" => array(2=>1, 3=>2, 5=>3));
        $activeCells = ActiveCells::fromJSON(json_encode((object)$data));
        $this->assertInstanceOf(ActiveCells::class, $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testThrowExceptionIfNotAnTwoDimensionalArray(){
        $data = array(1,2,3);
        $this->setExpectedException('InvalidArgumentException');
        ActiveCells::fromArray($data);
    }

    public function testThrowExceptionWithInvalidJson(){
        $data = "{{ 6: 239iu4";
        $this->setExpectedException('InvalidArgumentException');
        ActiveCells::fromJSON($data);
    }

}
