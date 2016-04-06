<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeologicalPointRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var  GeologicalPoint $geologicalPoint */
    protected $geologicalPoint;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser('GeologicalLayerTest');
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->geologicalPoint = GeologicalPointFactory::create()
            ->setName('GeologicalPointTest')
            ->setOwner($this->owner)
            ->setPublic(true)
        ;

        $this->entityManager->persist($this->geologicalPoint);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/geologicalpoints.json
     */
    public function testUserLayersListController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/geologicalpoints.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, json_decode($client->getResponse()->getContent()));
    }

    /**
     * Test for the API-Call /api/geologicalpoints.<id>.json
     */
    public function testProjectLayerDetailsController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/geologicalpoints/'.$this->geologicalPoint->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($this->geologicalPoint->getId(), json_decode($client->getResponse()->getContent())->id);
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

        $entities = $this->entityManager
            ->getRepository('AppBundle:GeologicalPoint')
            ->findAll();

        foreach ($entities as $entity)
        {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
