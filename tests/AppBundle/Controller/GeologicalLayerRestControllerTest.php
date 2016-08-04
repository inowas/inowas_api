<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalLayerFactory;
use Tests\AppBundle\RestControllerTestCase;

class GeologicalLayerRestControllerTest extends RestControllerTestCase
{
    /** @var  GeologicalLayer $geologicalLayer */
    protected $geologicalLayer;

    public function setUp()
    {
        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->flush();

        $this->geologicalLayer = GeologicalLayerFactory::create()
            ->setName('GeologicalLayerTest')
            ->setOwner($this->getOwner())
            ->setPublic(true)
            ->setOrder(GeologicalLayer::TOP_LAYER)
        ;

        $this->getEntityManager()->persist($this->geologicalLayer);
        $this->getEntityManager()->flush();
    }


    public function testListWithOutAPIKeyReturns401()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/unknown_username/geologicallayers.json');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/users/<username>/geologicallayers.json
     */
    public function testListByUser()
    {
        $client = static::createClient();
        $client->request('GET',
            '/api/users/'.$this->getOwner()->getUsername().'/geologicallayers.json',
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
            '/api/users/unknown_username/geologicallayers.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/geologicallayers.<id>.json
     */
    public function testDetailsById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/geologicallayers/'.$this->geologicalLayer->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($this->geologicalLayer->getId(), json_decode($client->getResponse()->getContent())->id);
    }

    public function testDetailsWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/geologicallayers/unknown_area_id.json',
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
            '/api/geologicallayers/ee3f68a1-7ffe-447c-9a67-bfe40850e1b8.json',
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

        $geologicalLayers = $this->getEntityManager()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findBy(array(
                'owner' => $user
            ));

        foreach ($geologicalLayers as $geologicalLayer) {
            $this->getEntityManager()->remove($geologicalLayer);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}
