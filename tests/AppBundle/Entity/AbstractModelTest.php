<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\UserFactory;

class AbstractModelTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModFlowModel */
    protected $modflowModel;

    public function setUp()
    {
        $this->modflowModel = new ModFlowModel();
    }

    public function testInstantiate()
    {

        $this->assertInstanceOf('AppBundle\Entity\ModFlowModel', $this->modflowModel);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->modflowModel->getId());
        $this->assertInstanceOf('\DateTime', $this->modflowModel->getDateCreated());
        $this->assertInstanceOf('\DateTime', $this->modflowModel->getDateModified());
        $this->assertTrue($this->modflowModel->getPublic());
    }

    public function testSetGetName()
    {
        $name = 'ModelName';
        $this->modflowModel->setName($name);
        $this->assertEquals($name, $this->modflowModel->getName());
    }

    public function testSetGetDescription()
    {
        $description = 'Description';
        $this->modflowModel->setDescription($description);
        $this->assertEquals($description, $this->modflowModel->getDescription());
    }

    public function testSetGetPublic()
    {
        $this->modflowModel->setPublic(false);
        $this->assertEquals(false, $this->modflowModel->getPublic());
        $this->modflowModel->setPublic(true);
        $this->assertEquals(true, $this->modflowModel->getPublic());
    }

    public function testSetGetDateModified()
    {
        $date = new \DateTime();
        $this->modflowModel->setDateModified($date);
        $this->assertEquals($date, $this->modflowModel->getDateModified());
    }

    public function testSetGetOwner()
    {
        $user = UserFactory::createTestUser('AbstractModel');
        $this->modflowModel->setOwner($user);
        $this->assertEquals($user, $this->modflowModel->getOwner());
    }


    protected function tearDown()
    {
        unset($this->modflowModel);
    }
}
