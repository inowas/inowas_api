<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\ToolFactory;

class ToolFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $this->assertInstanceOf('AppBundle\Entity\Tool', ToolFactory::create());
    }
}
