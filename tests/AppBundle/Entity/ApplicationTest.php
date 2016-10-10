<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Model\ApplicationFactory;
use AppBundle\Model\ToolFactory;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateId()
    {
        $application = ApplicationFactory::create();
        $this->assertInstanceOf('AppBundle\Entity\Application', $application);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $application->getId());
    }

    public function testSetGetName()
    {
        $name = "name";
        $application = ApplicationFactory::create();
        $application->setName($name);
        $this->assertEquals($name, $application->getName());
    }

    public function testSetGetDescription()
    {
        $description = "description";
        $application = ApplicationFactory::create();
        $application->setDescription($description);
        $this->assertEquals($description, $application->getDescription());
    }
    
    public function testAddGetRemoveTools()
    {
        $tool = ToolFactory::create();
        $application = ApplicationFactory::create();
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $application->getTools());
        $this->assertCount(0, $application->getTools());
        $application->addTool($tool);
        $this->assertCount(1, $application->getTools());
        $this->assertEquals($tool, $application->getTools()->first());
        $application->addTool($tool);
        $this->assertCount(1, $application->getTools());
        $application->removeTool($tool);
        $this->assertCount(0, $application->getTools());
    }
}
