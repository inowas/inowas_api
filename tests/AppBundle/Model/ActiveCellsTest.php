<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\ActiveCells;

class ActiveCellsTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateFromArray(){
        $data = array(array(1,2,3));
        $activeCells = ActiveCells::fromArray($data);
        $this->assertInstanceOf('AppBundle\Model\ActiveCells', $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testCreateFromArrayWithSingleFieldsSet(){
        $data[1][1] = true;
        $activeCells = ActiveCells::fromArray($data);
        $this->assertInstanceOf('AppBundle\Model\ActiveCells', $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testCreateFromObject(){
        $data = array("2" => array(2=>1, 3=>2, 5=>3));
        $activeCells = ActiveCells::fromObject((object)$data);
        $this->assertInstanceOf('AppBundle\Model\ActiveCells', $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testCreateFromJsonArray(){
        $data = array("2" => array(2=>1, 3=>2, 5=>3));
        $activeCells = ActiveCells::fromJSON(json_encode($data));
        $this->assertInstanceOf('AppBundle\Model\ActiveCells', $activeCells);
        $this->assertEquals($data,$activeCells->toArray());
    }

    public function testCreateFromJsonObject(){
        $data = array("2" => array(2=>1, 3=>2, 5=>3));
        $activeCells = ActiveCells::fromJSON(json_encode((object)$data));
        $this->assertInstanceOf('AppBundle\Model\ActiveCells', $activeCells);
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
