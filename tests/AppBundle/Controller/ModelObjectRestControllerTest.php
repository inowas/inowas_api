<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\BoundaryModelObject;
use AppBundle\Entity\ModelObject;
use AppBundle\Entity\User;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\UserFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModelObjectRestControllerTest extends WebTestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var  User */
    protected $owner;

    /** @var  User */
    protected $participant;

    /** @var ModelObject $modelObject */
    protected $modelObject;

    public function setUp()
    {
        self::bootKernel();
        
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;
        
        $this->owner = UserFactory::createTestUser('ModelObjectTestOwner');

        $this->participant = UserFactory::createTestUser('ModelObjectTestParticipant');

        $this->modelObject = GeneralHeadBoundaryFactory::create()
            ->setOwner($this->owner)
            ->setName('ModelObjectTestBoundaryName')
            ->setPublic(true);

        $this->entityManager->persist($this->owner);
        $this->entityManager->persist($this->participant);
        $this->entityManager->persist($this->modelObject);
        $this->entityManager->flush();
    }

    public function testList()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/modelobjects.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, json_decode($client->getResponse()->getContent()));
    }

    public function testListWithUnknownUserReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/unknown_username/modelobjects.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/modelobjects/'.$this->modelObject->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $modelObject = json_decode($client->getResponse()->getContent());
        $this->assertEquals($this->modelObject->getId(), $modelObject->id);
    }

    public function testDetailsWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/modelobjects/unknown_id.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDetailsWithUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/modelobjects/'.Uuid::uuid4()->toString().'.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        /** @var User $owner */
        $owner = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->owner->getUsername()
            ));
        $this->entityManager->remove($owner);

        /** @var User $participant */
        $participant = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->participant->getUsername()
            ));
        $this->entityManager->remove($participant);

        $boundaries = $this->entityManager->getRepository('AppBundle:ModelObject')
            ->findBy(array(
                'owner' => $owner
            ));

        foreach ($boundaries as $boundary) {
            if ($boundary instanceof BoundaryModelObject){
                $this->entityManager->remove($boundary);
            }
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
