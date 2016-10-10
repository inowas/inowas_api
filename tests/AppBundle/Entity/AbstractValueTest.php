<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Model\PropertyValueFactory;

class AbstractValueTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateId()
    {
        $value = PropertyValueFactory::create();
        $this->assertInstanceOf('AppBundle\Entity\PropertyValue', $value);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $value->getId());
    }
}
