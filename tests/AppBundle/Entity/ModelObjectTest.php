<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\PropertyValue;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;

class ModelObjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObservationPoint $modelObject
     */
    protected $modelObject;

    /**
     * @var PropertyType $propertyType
     */
    protected $propertyType;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->propertyType = PropertyTypeFactory::create()
            ->setName('TestPropertyType')
            ->setAbbreviation('tpt');

        $this->modelObject = ObservationPointFactory::create();
        $this->modelObject->setName('TestObservationPoint');
    }

    public function testCanAddValuesToModelObject()
    {
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(1)
        );
    }

    public function testAddingAValueWithANewPropertyTypeAddsANewProperty()
    {
        $this->assertCount(0, $this->modelObject->getProperties());
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(1)
        );
        $this->assertCount(1, $this->modelObject->getProperties());
    }

    public function testAddingValuesWithTheSamePropertyTypeAddsNoNewProperty()
    {
        $this->assertCount(0, $this->modelObject->getProperties());
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(1)
        );
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(2)
        );
        $this->assertCount(1, $this->modelObject->getProperties());
    }

    public function testAddStaticValueAddsANewValue()
    {
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(1.1)
        );

        $this->assertCount(1, $this->modelObject->getProperties());

        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(1, $property->getValues());

        /** @var PropertyValue $value */
        $value = $property->getValues()->first();
        $this->assertEquals(1.1, $value->getValue());

    }

    public function testAddStaticValueReplacesExistingStaticValue()
    {
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(1.1)
        );

        $this->assertCount(1, $this->modelObject->getProperties());

        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(1, $property->getValues());

        /** @var PropertyValue $value */
        $value = $property->getValues()->first();
        $this->assertEquals(1.1, $value->getValue());

        $this->modelObject->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setValue(2.2)
        );

        $this->assertCount(1, $this->modelObject->getProperties());

        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(1, $property->getValues());

        /** @var PropertyValue $value */
        $value = $property->getValues()->first();
        $this->assertEquals(2.2, $value->getValue());
    }

    public function testAddTimeValue()
    {
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyTimeValueFactory::create()
                ->setDatetime(new \DateTime('2015-12-12'))
                ->setValue(1.1)
        );

        $this->assertCount(1, $this->modelObject->getProperties());

        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(1, $property->getValues());

        /** @var PropertyValue $value */
        $value = $property->getValues()->first();
        $this->assertEquals(1.1, $value->getValue());
        $this->assertEquals(new \DateTime('2015-12-12'), $value->getDateBegin());
        $this->assertEquals(new \DateTime('2015-12-12'), $value->getDateEnd());
    }

    public function testAddTimeValueWithDifferentTimesAggregatesAValue()
    {
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyTimeValueFactory::create()
                ->setDatetime(new \DateTime('2015-12-12'))
                ->setValue(1.1)
        );

        $this->assertCount(1, $this->modelObject->getProperties());
        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(1, $property->getValues());

        $this->modelObject->addValue(
            $this->propertyType,
            PropertyTimeValueFactory::create()
                ->setDatetime(new \DateTime('2015-12-13'))
                ->setValue(2.2)
        );

        $this->assertCount(1, $this->modelObject->getProperties());

        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(2, $property->getValues());

        /** @var PropertyValue $value */
        $value = $property->getValues()->last();
        $this->assertEquals(2.2, $value->getValue());
        $this->assertEquals(new \DateTime('2015-12-13'), $value->getDateBegin());
        $this->assertEquals(new \DateTime('2015-12-13'), $value->getDateEnd());
    }

    public function testAddTimeValueWithSameTimesReplacesAValue()
    {
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyTimeValueFactory::create()
                ->setDatetime(new \DateTime('2015-12-12'))
                ->setValue(1.1)
        );

        $this->assertCount(1, $this->modelObject->getProperties());
        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(1, $property->getValues());

        $this->modelObject->addValue(
            $this->propertyType,
            PropertyTimeValueFactory::create()
                ->setDatetime(new \DateTime('2015-12-12'))
                ->setValue(2.2)
        );

        $this->assertCount(1, $this->modelObject->getProperties());

        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(1, $property->getValues());

        /** @var PropertyValue $value */
        $value = $property->getValues()->first();
        $this->assertEquals(2.2, $value->getValue());
        $this->assertEquals(new \DateTime('2015-12-12'), $value->getDateBegin());
        $this->assertEquals(new \DateTime('2015-12-12'), $value->getDateEnd());
    }
}
