<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalPointFactory;
use Tests\AppBundle\RestControllerTestCase;

class GeologicalPointRestControllerTest extends RestControllerTestCase
{

    /** @var  GeologicalPoint $geologicalPoint */
    protected $geologicalPoint;

    public function setUp()
    {
        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->flush();

        $this->geologicalPoint = GeologicalPointFactory::create()
            ->setName('GeologicalPointTest')
            ->setOwner($this->getOwner())
            ->setPublic(true)
        ;

        $this->getEntityManager()->persist($this->geologicalPoint);
        $this->getEntityManager()->flush();
    }

    public function testListWithoutAPIKeyThrows401()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/unknown_username/geologicalpoints.json');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/users/<username>/geologicalpoints.json
     */
    public function testListByUser()
    {
        $client = static::createClient();
        $client->request(
            'GET', '/api/users/'.$this->getOwner()->getUsername().'/geologicalpoints.json',
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
            '/api/users/unknown_username/geologicalpoints.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/geologicalpoints.<id>.json
     */
    public function testDetailsById()
    {
        $client = static::createClient();
        $client->request('GET', '/api/geologicalpoints/'.$this->geologicalPoint->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($this->geologicalPoint->getId(), json_decode($client->getResponse()->getContent())->id);
    }

    public function testDetailsWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/geologicalpoints/unknown_area_id.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDetailsWithUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/geologicalpoints/ee3f68a1-7ffe-447c-9a67-bfe40850e1b8.json',
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
            ->getRepository('AppBundle:GeologicalPoint')
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
