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

    public function testThrowExceptionIfNotAnTwoDimansionalArray(){
        $data = array(1,2,3);
        $this->setExpectedException('InvalidArgumentException');
        ActiveCells::fromArray($data);
    }

}
