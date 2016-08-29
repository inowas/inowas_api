<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\StreamBoundary;
use AppBundle\Entity\User;
use AppBundle\Model\StreamBoundaryFactory;
use Tests\AppBundle\RestControllerTestCase;

class StreamRestControllerTest extends RestControllerTestCase
{

    /** @var  StreamBoundary $boundary */
    protected $stream;

    public function setUp()
    {
        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->flush();

        $this->stream = StreamBoundaryFactory::create()
            ->setName('Stream')
            ->setPublic(true)
            ->setOwner($this->getOwner())
        ;

        $this->getEntityManager()->persist($this->stream);
        $this->getEntityManager()->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/streams.json
     */
    public function testStreamList()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/'.$this->getOwner()->getUsername().'/streams.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $streams = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, count($streams));
        $this->assertEquals($this->stream->getName(), $streams[0]->name);
    }

    /**
     * Test for the API-Call /api/streams/<id>.json
     */
    public function testStreamDetails()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/streams/'.$this->stream->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $area = json_decode($client->getResponse()->getContent());
        $this->assertEquals($area->id, $this->stream->getId());
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

        $entities = $this->getEntityManager()->getRepository('AppBundle:StreamBoundary')
            ->findBy(array(
                'owner' => $user
            ));

        foreach ($entities as $entity) {
            $this->getEntityManager()->remove($entity);
        }

        $this->getEntityManager()->flush();
    }
}
