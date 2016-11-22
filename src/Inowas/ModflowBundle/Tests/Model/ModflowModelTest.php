<?php

namespace Inowas\ModflowBundle\Tests\Model;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;

class ModflowModelTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ModflowModel */
    protected $model;

    public function setUp(){
        $this->model = ModflowModelFactory::create()
            ->setStart(new \DateTime('2016-01-01'))
            ->setEnd(new \DateTime('2016-12-31'));
    }

    public function testInstantiate(){
        $this->assertInstanceOf(ModflowModel::class, $this->model);
    }
}
