<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeologicalUnitRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var  GeologicalUnit $geologicalUnit */
    protected $geologicalUnit;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser('GeologicalLayerTest');
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->geologicalUnit = GeologicalUnitFactory::create()
            ->setName('GeologicalUnitTest')
            ->setOwner($this->owner)
            ->setPublic(true)
        ;

        $this->entityManager->persist($this->geologicalUnit);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/geologicalunits.json
     */
    public function testUserLayersListController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/geologicalunits.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, json_decode($client->getResponse()->getContent()));
    }

    /**
     * Test for the API-Call /api/geologicalunits.<id>.json
     */
    public function testProjectLayerDetailsController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/geologicalunits/'.$this->geologicalUnit->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($this->geologicalUnit->getId(), json_decode($client->getResponse()->getContent())->id);
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
            ->getRepository('AppBundle:GeologicalUnit')
            ->findAll();

        foreach ($entities as $entity)
        {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
