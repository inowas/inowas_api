<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AreaRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var  User $participant */
    protected $participant;

    /** @var AreaType $areaType*/
    protected $areaType;

    /** @var  Area $area */
    protected $area;


    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser('AreaOwner');
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->participant = UserFactory::createTestUser('AreaParticipant');
        $this->entityManager->persist($this->participant);
        $this->entityManager->flush();

        $this->areaType = AreaTypeFactory::setName('ModelAreaType');
        $this->entityManager->persist($this->areaType);
        $this->entityManager->flush();

        $this->area = AreaFactory::setOwnerNameTypeAndPublic($this->owner, 'ModelArea1', $this->areaType, true);
        $this->entityManager->persist($this->area);
        $this->entityManager->flush();

        $this->area = AreaFactory::setOwnerNameTypeAndPublic($this->owner, 'ModelArea2', $this->areaType, true);
        $this->entityManager->persist($this->area);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/areas.json
     * which is providing a list of areas of the user
     */
    public function testAreaList()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/areas.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $modelAreas = json_decode($client->getResponse()->getContent());
        $this->assertEquals(2, count($modelAreas));

        $this->assertEquals($this->area->getName(), $modelAreas[1]->name);
    }

    /**
     * Test for the API-Call /api/areas/<id>.json
     * which is providing a the details of a specific areas of the user
     */
    public function testAreaDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/areas/'.$this->area->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $area = json_decode($client->getResponse()->getContent());
        $this->assertEquals($area->id, $this->area->getId());
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

        $participant = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->participant->getUsername()
            ));
        $this->entityManager->remove($participant);

        $areas = $this->entityManager->getRepository('AppBundle:Area')
            ->findAll();

        foreach ($areas as $area)
        {
            $this->entityManager->remove($area);
        }

        $areaType = $this->entityManager->getRepository('AppBundle:AreaType')
            ->findOneBy(array(
               'id' => $this->areaType->getId()
            ));
        $this->entityManager->remove($areaType);

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
