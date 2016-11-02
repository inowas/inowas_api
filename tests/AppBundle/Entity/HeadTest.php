<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Head;
use Ramsey\Uuid\Uuid;

class HeadTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Head */
    private $head;

    public function setUp(){
        $this->head = new Head();
    }

    public function testInstantiate(){
        $this->assertInstanceOf(Head::class, $this->head);
    }

    public function testIdIsUuid(){
        $this->assertInstanceOf(Uuid::class, $this->head->getId());
    }

    public function testSetGetTotim(){
        $totim = 1;
        $this->assertEquals($totim, $this->head->setTotim($totim)->getTotim());
    }

    public function testSetGetModelId(){
        $modelId = Uuid::uuid4();
        $this->assertEquals($modelId, $this->head->setModelId($modelId)->getModelId());
    }

    public function testSetGetLayer(){
        $layer = 1;
        $this->assertEquals($layer, $this->head->setLayer($layer)->getLayer());
    }

    public function testSetDataAndCalculateMinMax(){
        $data = array(
            [-1.3, 0.2, 0.3, 0.4, 0.5],
            [0.1, 0.2, 0.3, 0.4, 0.5],
            [0.1, 0.2, 0.3, 0.4, 0.5],
            [0.1, 0.2, 0.3, 0.4, 0.5],
            [0.1, 0.2, 0.3, 0.4, 0.5],
            [0.1, 0.2, 0.3, 0.4, 0.57]
        );

        $this->head->setData($data);
        $this->assertEquals(-1.3, $this->head->getMin());
        $this->assertEquals(0.57, $this->head->getMax());
    }
}
