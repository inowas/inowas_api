<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\GeologicalUnit;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;

class GeologicalUnitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GeologicalUnit $modelObject
     */
    protected $geologicalUnit;

    /**
     * @var PropertyType $propertyType
     */
    protected $propertyType;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->propertyType = PropertyTypeFactory::create(PropertyType::TOP_ELEVATION);
        $this->geologicalUnit = GeologicalUnitFactory::create()
            ->setName('TestGeologicalUnit');
    }

    public function testCanAddValuesToModelObject()
    {
        $this->geologicalUnit->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(20.5)
        );
    }

    public function testGetTopElevationReturnsNullIfNoElevationIsGiven() {
        $this->assertNull($this->geologicalUnit->getTopElevation());
    }

    public function testGetBottomElevationReturnsNullIfNoElevationIsGiven() {
        $this->assertNull($this->geologicalUnit->getBottomElevation());
    }

    public function testGetTopElevation() {
        $this->propertyType = PropertyTypeFactory::create(PropertyType::TOP_ELEVATION);
        $this->geologicalUnit->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(20.5)
        );

        $this->assertEquals(20.5, $this->geologicalUnit->getTopElevation());
    }

    public function testGetBottomElevation() {
        $this->propertyType = PropertyTypeFactory::create(PropertyType::BOTTOM_ELEVATION);
        $this->geologicalUnit->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(21.5)
        );

        $this->assertEquals(21.5, $this->geologicalUnit->getBottomElevation());
    }

}
