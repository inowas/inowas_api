<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Project;
use AppBundle\Entity\PropertyFixedIntervalValue;
use AppBundle\Entity\User;
use AppBundle\Entity\UserProfile;
use AppBundle\Model\BoundaryFactory;
use AppBundle\Model\ProjectFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyTypeFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PropertyTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var User $user
     */
    private $user;

    /**
     * @var Project $project
     */
    private $project;

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
    }

    public function testSetBeginDatePrePersistMethod()
    {
        // Setup
        $user = new User();
        $user->setUsername('testUser_2');
        $user->setEmail('testUsersEmail');
        $user->setPassword('usersPassword');
        $user->setEnabled(true);
        $this->entityManager->persist($user);

        $project = ProjectFactory::setOwnerAndPublic($this->user, true);
        $project->setName('TestProject');
        $this->entityManager->persist($project);

        $boundary = BoundaryFactory::setOwnerProjectNameAndPublic($user, $project, 'testBoundary', true);
        $this->entityManager->persist($boundary);

        $propertyType = PropertyTypeFactory::setName('testProperty');
        $this->entityManager->persist($propertyType);

        $property = PropertyFactory::setTypeAndModelObject($propertyType, $boundary);
        $this->entityManager->persist($property);
        $this->entityManager->flush();

        $this->assertEquals(0, $property->getNumberOfValues());
        $property->addValue(PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime(), 5));
        $this->entityManager->persist($property);
        $this->entityManager->flush();

        $this->assertEquals(1, $property->getNumberOfValues());
        $property->addValue(PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime(), 6));
        $this->entityManager->persist($property);
        $this->entityManager->flush();

        $property->addValue(PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime('2010-01-01'), 6));
        $property->addValue(PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime('2020-01-01'), 6));
        $this->entityManager->persist($property);
        $this->entityManager->flush();

        $this->assertEquals(4, $property->getNumberOfValues());
        $this->assertEquals(new \DateTime('2010-01-01'), $property->getDateTimeBegin());
        $this->assertEquals(new \DateTime('2020-01-01'), $property->getDateTimeEnd());


        $value = new PropertyFixedIntervalValue();
        $value->setDateTimeBegin(new \DateTime('2000-01-01'));
        $value->setDateTimeInterval('P1D');
        $value->setValues(array(1,2,3,4,5,6,7,8,9,10));
        $value->setProperty($property);
        $property->addValue($value);
        $this->entityManager->persist($property);
        $this->entityManager->flush();

        $this->assertEquals(14, $property->getNumberOfValues());
        $this->assertEquals(new \DateTime('2000-01-01'), $property->getDateTimeBegin());


        // Teardown
        $this->entityManager->remove($user);
        $this->entityManager->remove($project);
        $this->entityManager->remove($boundary);
        $this->entityManager->remove($property);
        $this->entityManager->flush();
        $this->entityManager->close();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
}
