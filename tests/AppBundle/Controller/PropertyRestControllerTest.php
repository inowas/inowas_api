<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Property;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyFixedIntervalValueFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PropertyRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var Property $area */
    protected $property;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->property = PropertyFactory::create()
            ->setName('PropertyControllerTest')
            ->addValue(PropertyTimeValueFactory::create()
                ->setDatetime(new \DateTime(2015-01-01))
                ->setValue(12.1))
            ->addValue(PropertyTimeValueFactory::create()
                ->setDatetime(new \DateTime(2015-01-02))
                ->setValue(13.1))
            ->addValue(PropertyFixedIntervalValueFactory::create()
                ->setDateTimeBegin(new \DateTime(2015-01-03))
                ->setDateTimeIntervalString('P1D')
                ->setValues(array(14.1, 15.1, 16.1, 17.1, 18.1, 19.1))
            );

        $this->entityManager->persist($this->property);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/properties/<id>.json
     * which is providing a list of areas of the user
     */
    public function testPropertyCallDetailsWithoutDates()
    {
        $client = static::createClient();
        $client->request('GET', '/api/properties/'.$this->property->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $property = json_decode($client->getResponse()->getContent());
        $this->assertEquals($this->property->getId(), $property->id);
        $this->assertEquals($this->property->getName(), $property->name);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $property = $this->entityManager
            ->getRepository('AppBundle:Property')
            ->findOneBy(array(
                'name' => $this->property->getName()
            ));

        $this->entityManager->remove($property);
        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
