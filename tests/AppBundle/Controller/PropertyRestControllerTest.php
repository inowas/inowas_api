<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Property;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyFixedIntervalValueFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use Ramsey\Uuid\Uuid;
use Tests\AppBundle\RestControllerTestCase;

class PropertyRestControllerTest extends RestControllerTestCase
{

    /** @var Property $area */
    protected $property;

    public function setUp()
    {
        $this->getEntityManager()->persist($this->getOwner());

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

        $this->getEntityManager()->persist($this->property);
        $this->getEntityManager()->flush();
    }

    /**
     * Test for the API-Call /api/properties/<id>.json
     * which is providing a list of areas of the user
     */
    public function testGetPropertyById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/properties/'.$this->property->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $property = json_decode($client->getResponse()->getContent());
        $this->assertEquals($this->property->getId(), $property->id);
        $this->assertEquals($this->property->getName(), $property->name);
    }

    public function testGetPropertyByInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/properties/123.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetPropertyByUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/properties/'.Uuid::uuid4()->toString().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $property = $this->getEntityManager()
            ->getRepository('AppBundle:Property')
            ->findOneBy(array(
                'name' => $this->property->getName()
            ));

        $owner = $this->getEntityManager()->getRepository('AppBundle:User')->findOneBy(array('id' => $this->getOwner()->getId()->toString()));
        $this->getEntityManager()->remove($owner);
        $this->getEntityManager()->remove($property);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}
