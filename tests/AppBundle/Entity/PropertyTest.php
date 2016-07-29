<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyFixedIntervalValue;
use AppBundle\Entity\User;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PropertyTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var GeneralHeadBoundary $boundary
     */
    protected $boundary;

    /**
     * @var Property $property
     */
    protected $property;

    /**
     * @var PropertyType $propertyType
     */
    protected $propertyType;


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        // Setup
        $this->user = UserFactory::create();
        $this->user->setUsername('testUser_2');
        $this->user->setEmail('testUsersEmail');
        $this->user->setPassword('usersPassword');
        $this->user->setEnabled(true);
        $this->entityManager->persist($this->user);

        $this->boundary = GeneralHeadBoundaryFactory::create()
            ->setOwner($this->user)
            ->setName('testBoundary')
            ->setPublic(true)
        ;
        $this->entityManager->persist($this->boundary);
        $this->propertyType = PropertyTypeFactory::create(PropertyType::TOP_ELEVATION);

        /** @var Property property */
        $this->property = PropertyFactory::create()
            ->setPropertyType($this->propertyType);

        $this->entityManager->persist($this->property);
        $this->entityManager->flush();
    }

    public function testSetGetName()
    {
        $name = "name";
        $this->property->setName($name);
        $this->assertEquals($name, $this->property->getName());
        $this->property->setName(null);
        $this->assertEquals("", $this->property->getName());
    }

    public function testSetGetDescription()
    {
        $description = "description";
        $this->property->setDescription($description);
        $this->assertEquals($description, $this->property->getDescription());
        $this->property->setDescription(null);
        $this->assertEquals("", $this->property->getDescription());
    }

    public function testAddGetRemoveValues()
    {
        $value = PropertyValueFactory::create();
        $this->assertCount(0, $this->property->getValues());
        $this->property->addValue($value);
        $this->assertCount(1, $this->property->getValues());
        $this->property->addValue($value);
        $this->assertCount(1, $this->property->getValues());
        $this->assertEquals(1, $this->property->getNumberOfValues());
        $this->property->removeValue($value);
        $this->assertCount(0, $this->property->getValues());
    }

    public function testGetTimeValues()
    {
        $timeValue1 = PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2016-01-01'), 1.1);
        $timeValue2 = PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2016-01-02'), 1.2);
        $this->property->addValue($timeValue1);
        $this->assertCount(1, $this->property->getTimeValues());
        $this->property->addValue($timeValue2);
        $this->assertCount(2, $this->property->getTimeValues());
    }

    public function testRecalculateNumberOfValuesWithPropertyValuePreFlush()
    {
        $this->assertCount(0, $this->property->getValues());
        $this->assertEquals(0, $this->property->getNumberOfValues());
        $this->property->addValue(PropertyValueFactory::create()->setValue(5));
        $this->entityManager->persist($this->property);

        $this->assertCount(1, $this->property->getValues());
        $this->assertEquals(1, $this->property->getNumberOfValues());
    }

    public function testRecalculateNumberOfValuesWithPropertyDateTimeValuePreFlush()
    {
        $this->assertCount(0, $this->property->getValues());
        $this->assertEquals(0, $this->property->getNumberOfValues());
        $this->property->addValue(PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime(), 5));
        $this->entityManager->persist($this->property);

        $this->assertCount(1, $this->property->getValues());
        $this->assertEquals(1, $this->property->getNumberOfValues());
    }

    public function testRecalculateNumberOfValuesWithPropertyFixedIntervalValuePreFlush()
    {
        $this->assertCount(0, $this->property->getValues());
        $this->assertEquals(0, $this->property->getNumberOfValues());

        $value = new PropertyFixedIntervalValue();
        $value->setDateTimeBegin(new \DateTime('2000-01-01'));
        $value->setDateTimeIntervalString('P1D');
        $value->setValues(array(1,2,3,4,5,6,7,8,9,10));
        $this->property->addValue($value);
        $this->entityManager->persist($this->property);
        $this->assertCount(1, $this->property->getValues());
        $this->assertEquals(10, $this->property->getNumberOfValues());
    }

    public function testRecalculateStartDateWithPropertyValuePreFlush()
    {
        $this->assertNull($this->property->getDateTimeBegin());
        $this->property->addValue(PropertyValueFactory::create()->setValue(5));
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertNull($this->property->getDateTimeBegin());
    }

    public function testRecalculateStartDateWithPropertyDateTimeValuePreFlush()
    {
        $startDate = new \DateTime('2011-11-11');
        $earlierStartDate = new \DateTime('2010-10-10');

        $this->assertCount(0, $this->property->getValues());
        $this->assertNull($this->property->getDateTimeBegin());
        $this->property->addValue(PropertyTimeValueFactory::createWithTimeAndValue($startDate, 5));
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertCount(1, $this->property->getValues());
        $this->assertEquals(1, $this->property->getNumberOfValues());
        $this->assertEquals($startDate, $this->property->getDateTimeBegin());

        $this->property->addValue(PropertyTimeValueFactory::createWithTimeAndValue($earlierStartDate, 6));
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertCount(2, $this->property->getValues());
        $this->assertEquals(2, $this->property->getNumberOfValues());
        $this->assertEquals($earlierStartDate, $this->property->getDateTimeBegin());
    }

    public function testRecalculateStartDateWithPropertyFixedIntervalValuePreFlush()
    {
        $startDate = new \DateTime('2011-11-11');
        $earlierStartDate = new \DateTime('2010-10-10');

        $this->assertCount(0, $this->property->getValues());
        $this->assertNull($this->property->getDateTimeBegin());

        $value = new PropertyFixedIntervalValue();
        $value->setDateTimeBegin($startDate);
        $value->setDateTimeIntervalString('P1D');
        $value->setValues(array(1,2,3,4,5,6,7,8,9,10));
        $this->property->addValue($value);
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertEquals($startDate, $this->property->getDateTimeBegin());

        $value = new PropertyFixedIntervalValue();
        $value->setDateTimeBegin($earlierStartDate);
        $value->setDateTimeIntervalString('P1D');
        $value->setValues(array(1,2,3,4,5,6,7,8,9,10));
        $this->property->addValue($value);
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertEquals($earlierStartDate, $this->property->getDateTimeBegin());
    }

    public function testRecalculateEndDateWithPropertyValuePreFlush()
    {
        $this->assertNull($this->property->getDateTimeEnd());
        $this->property->addValue(PropertyValueFactory::create()->setValue(5));
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertNull($this->property->getDateTimeEnd());
    }

    public function testRecalculateEndDateWithPropertyDateTimeValuePreFlush()
    {
        $endDate = new \DateTime('2011-11-11');
        $laterEndDate = new \DateTime('2012-12-12');

        $this->assertCount(0, $this->property->getValues());
        $this->assertNull($this->property->getDateTimeEnd());
        $this->property->addValue(PropertyTimeValueFactory::createWithTimeAndValue($endDate, 5));
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertCount(1, $this->property->getValues());
        $this->assertEquals(1, $this->property->getNumberOfValues());
        $this->assertEquals($endDate, $this->property->getDateTimeEnd());

        $this->property->addValue(PropertyTimeValueFactory::createWithTimeAndValue($laterEndDate, 6));
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertCount(2, $this->property->getValues());
        $this->assertEquals(2, $this->property->getNumberOfValues());
        $this->assertEquals($laterEndDate, $this->property->getDateTimeEnd());
    }

    public function testRecalculateEndDateWithPropertyFixedIntervalValuePreFlush()
    {
        $startDate = new \DateTime('2010-10-10');
        $expectedEndDate = new \DateTime('2010-10-20');

        $laterStartDate = new \DateTime('2011-11-11');
        $laterExpectedEndDate = new \DateTime('2011-11-21');

        $this->assertCount(0, $this->property->getValues());
        $this->assertNull($this->property->getDateTimeBegin());
        $this->assertNull($this->property->getDateTimeEnd());

        $value = new PropertyFixedIntervalValue();
        $value->setDateTimeBegin($startDate);
        $value->setDateTimeIntervalString('P1D');
        $value->setValues(array(1,2,3,4,5,6,7,8,9,10));
        $this->property->addValue($value);
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertEquals($startDate, $this->property->getDateTimeBegin());
        $this->assertEquals($expectedEndDate, $this->property->getDateTimeEnd());

        $value = new PropertyFixedIntervalValue();
        $value->setDateTimeBegin($laterStartDate);
        $value->setDateTimeIntervalString('P1D');
        $value->setValues(array(1,2,3,4,5,6,7,8,9,10));
        $this->property->addValue($value);
        $this->entityManager->persist($this->property);
        $this->entityManager->flush();

        $this->assertEquals($startDate, $this->property->getDateTimeBegin());
        $this->assertEquals($laterExpectedEndDate, $this->property->getDateTimeEnd());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->entityManager->remove($this->user);
        $this->entityManager->remove($this->boundary);
        $this->entityManager->remove($this->property);
        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
