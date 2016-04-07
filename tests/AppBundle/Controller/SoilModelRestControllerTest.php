<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\SoilModel;
use AppBundle\Entity\User;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SoilModelRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var SoilModel $soilModel */
    protected $soilModel;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser("SoilModelTestOwner");
        $this->entityManager->persist($this->owner);

        $this->soilModel = SoilModelFactory::create();
        $this->soilModel->setOwner($this->owner);
        $this->soilModel->setPublic(true);
        $this->soilModel->setName('SoilModelTest');
        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/soilmodels.json
     */
    public function testGetListOfSoilModelsByUserAPI()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/soilmodels.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        dump($client->getResponse()->getContent());
        $soilModels = json_decode($client->getResponse()->getContent());

        $this->assertEquals(1, count($soilModels));
        //$this->assertEquals($this->soilModel->getName(), $soilModels[0]->name);
    }

    public function testGetSoilModelDetailsAPI()
    {
        $client = static::createClient();
        $client->request('GET', '/api/soilmodels/'.$this->soilModel->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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

        $soilModel = $this->entityManager->getRepository('AppBundle:SoilModel')
            ->findOneBy(array(
               'name' => $this->soilModel->getName()
            ));
        $this->entityManager->remove($soilModel);
        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
