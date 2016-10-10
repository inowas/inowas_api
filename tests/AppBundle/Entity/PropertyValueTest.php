<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\PropertyValue;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RasterFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PropertyValueTest extends WebTestCase
{

    /**
     * @var PropertyValue
     */
    protected $propertyValue;


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->propertyValue = PropertyValueFactory::create();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf('AppBundle\Entity\PropertyValue', $this->propertyValue);
    }

    public function testSetGetHasValue()
    {
        $this->assertFalse($this->propertyValue->hasValue());
        $this->propertyValue->setValue(1.1);
        $this->assertEquals(1.1, $this->propertyValue->getValue());
        $this->assertTrue($this->propertyValue->hasValue());
    }

    public function testGetTimeValues()
    {
        $this->assertInstanceOf('AppBundle\Model\TimeValue', $this->propertyValue->getTimeValues()[0]);
    }

    public function testHasRaster()
    {
        $this->assertFalse($this->propertyValue->hasRaster());
        $this->propertyValue->setRaster(RasterFactory::create());
        $this->assertTrue($this->propertyValue->hasRaster());
    }



    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        unset($this->propertyValue);
    }
}
