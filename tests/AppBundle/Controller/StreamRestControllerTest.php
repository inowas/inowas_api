<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\StreamBoundary;
use AppBundle\Entity\User;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StreamRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var  StreamBoundary $boundary */
    protected $stream;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser('BoundaryOwner');
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->stream = StreamBoundaryFactory::create()
            ->setName('Stream')
            ->setPublic(true)
            ->setOwner($this->owner)
        ;

        $this->entityManager->persist($this->stream);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/streams.json
     */
    public function testStreamList()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/streams.json');
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
        $client->request('GET', '/api/streams/'.$this->stream->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $area = json_decode($client->getResponse()->getContent());
        $this->assertEquals($area->id, $this->stream->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->owner->getUsername()
            ));
        $this->entityManager->remove($user);

        $entities = $this->entityManager->getRepository('AppBundle:StreamBoundary')
            ->findBy(array(
                'owner' => $user
            ));

        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
