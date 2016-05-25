<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeologicalLayerRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var  GeologicalLayer $geologicalLayer */
    protected $geologicalLayer;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser('GeologicalLayerTest');
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->geologicalLayer = GeologicalLayerFactory::create()
            ->setName('GeologicalLayerTest')
            ->setOwner($this->owner)
            ->setPublic(true)
        ;

        $this->entityManager->persist($this->geologicalLayer);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/geologicallayers.json
     */
    public function testUserLayersListController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/geologicallayers.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, json_decode($client->getResponse()->getContent()));
    }

    /**
     * Test for the API-Call /api/geologicallayers.<id>.json
     */
    public function testProjectLayerDetailsController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/geologicallayers/'.$this->geologicalLayer->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($this->geologicalLayer->getId(), json_decode($client->getResponse()->getContent())->id);
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

        $geologicalLayers = $this->entityManager
            ->getRepository('AppBundle:GeologicalLayer')
            ->findAll();

        foreach ($geologicalLayers as $geologicalLayer)
        {
            $this->entityManager->remove($geologicalLayer);
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
