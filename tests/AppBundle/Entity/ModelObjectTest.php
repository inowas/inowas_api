<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Raster;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RasterFactory;
use AppBundle\Model\UserFactory;

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

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\ModelObject', $this->modelObject);
        $this->assertInstanceOf('AppBundle\Entity\ObservationPoint', $this->modelObject);
    }

    public function testSetGetPublic()
    {
        $this->modelObject->setPublic(true);
        $this->assertTrue($this->modelObject->getPublic());
        $this->modelObject->setPublic(false);
        $this->assertFalse($this->modelObject->getPublic());
    }

    public function testSetGetUpdateDates()
    {
        $this->assertInstanceOf('\DateTime', $this->modelObject->getDateCreated());
        $date = new \DateTime('2015-01-01');
        $this->modelObject->setDateModified($date);
        $this->assertInstanceOf('\DateTime', $this->modelObject->getDateModified());
        $this->assertEquals($date, $this->modelObject->getDateModified());
        $this->modelObject->updateDateModified();
        $this->assertInstanceOf('\DateTime', $this->modelObject->getDateModified());
        $this->assertEquals(new \DateTime(), $this->modelObject->getDateModified());
    }

    public function testGetNameOfClass()
    {
        $this->assertEquals('AppBundle\Entity\ObservationPoint', $this->modelObject->getNameOfClass());
    }

    public function testSetGetOwner()
    {
        $user = UserFactory::create();
        $this->modelObject->setOwner($user);
        $this->assertEquals($user, $this->modelObject->getOwner());
    }

    public function testAddGetRemoveProperties()
    {
        $property = PropertyFactory::create();
        $this->assertCount(0, $this->modelObject->getProperties());
        $this->modelObject->addProperty($property);
        $this->assertCount(1, $this->modelObject->getProperties());
        $this->modelObject->addProperty($property);
        $this->assertCount(1, $this->modelObject->getProperties());
        $this->modelObject->removeProperty($property);
        $this->assertCount(0, $this->modelObject->getProperties());
    }

    public function testGetPropertyIds()
    {
        $property = PropertyFactory::create();
        $this->modelObject->addProperty($property);
        $this->assertCount(1, $this->modelObject->getPropertyIds());
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->modelObject->getPropertyIds()[0]);
    }

    public function testAddGetRemoveObservationPoints(){
        $observationPoint = ObservationPointFactory::create();
        $this->assertCount(0, $this->modelObject->getObservationPoints());
        $this->modelObject->addObservationPoint($observationPoint);
        $this->assertCount(1, $this->modelObject->getObservationPoints());
        $this->modelObject->addObservationPoint($observationPoint);
        $this->assertCount(1, $this->modelObject->getObservationPoints());
        $this->assertEquals($observationPoint, $this->modelObject->getObservationPoints()->first());
        $anotherObservationPoint = ObservationPointFactory::create();
        $this->modelObject->addObservationPoint($anotherObservationPoint);
        $this->assertCount(2, $this->modelObject->getObservationPoints());
        $this->modelObject->removeObservationPoint($observationPoint);
        $this->modelObject->removeObservationPoint($anotherObservationPoint);
        $this->assertCount(0, $this->modelObject->getObservationPoints());
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

    public function testAddRasterValueReplacesExistingStaticValue()
    {
        $this->modelObject->addValue(
            $this->propertyType,
            PropertyValueFactory::create()->setRaster(
                RasterFactory::create()
                ->setDescription('New Raster')
            )
        );

        /** @var Property $property */
        $property = $this->modelObject->getProperties()->first();
        $this->assertCount(1, $property->getValues());

        /** @var PropertyValue $value */
        $value = $property->getValues()->first();
        $this->assertNull($value->getValue());
        $this->assertTrue($value->getRaster() instanceof Raster);
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
