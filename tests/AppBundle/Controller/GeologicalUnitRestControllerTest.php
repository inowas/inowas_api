<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalUnitFactory;
use Tests\AppBundle\RestControllerTestCase;

class GeologicalUnitRestControllerTest extends RestControllerTestCase
{

    /** @var  GeologicalUnit $geologicalUnit */
    protected $geologicalUnit;

    public function setUp()
    {
        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->flush();

        $this->geologicalUnit = GeologicalUnitFactory::create()
            ->setOrder(GeologicalUnit::TOP_LAYER)
            ->setName('GeologicalUnitTest')
            ->setOwner($this->getOwner())
            ->setPublic(true)
        ;

        $this->getEntityManager()->persist($this->geologicalUnit);
        $this->getEntityManager()->flush();
    }

    public function testListWithoutAIKeyReturns401()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/unknown_username/geologicalunits.json'
        );
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/users/<username>/geologicalunits.json
     */
    public function testListByUser()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/'.$this->getOwner()->getUsername().'/geologicalunits.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, json_decode($client->getResponse()->getContent()));
    }

    public function testListWithUnknownUserReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/unknown_username/geologicalunits.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/geologicalunits.<id>.json
     */
    public function testDetailsById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/geologicalunits/'.$this->geologicalUnit->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($this->geologicalUnit->getId(), json_decode($client->getResponse()->getContent())->id);
    }

    public function testDetailsWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/geologicalunits/unknown_area_id.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDetailsWithUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/geologicalunits/ee3f68a1-7ffe-447c-9a67-bfe40850e1b8.json',
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
        $user = $this->getEntityManager()->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->getOwner()->getUsername()
            ));
        $this->getEntityManager()->remove($user);

        $entities = $this->getEntityManager()
            ->getRepository('AppBundle:GeologicalUnit')
            ->findBy(array(
                'owner' => $user
            ));

        foreach ($entities as $entity) {
            $this->getEntityManager()->remove($entity);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}
