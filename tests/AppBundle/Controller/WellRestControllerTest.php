<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Well;
use AppBundle\Model\Point;
use AppBundle\Model\UserFactory;
use AppBundle\Model\WellFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WellRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var  Well $well */
    protected $well;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser('BoundaryOwner');
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->well = WellFactory::create()
            ->setName('Well')
            ->setPublic(true)
            ->setOwner($this->owner)
            ->setPoint(new Point(10.1, 11.1, 3568))
        ;

        $this->entityManager->persist($this->well);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/wells.json
     */
    public function testWellList()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/wells.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $wells = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, count($wells));
        $well = $wells[0];
        $this->assertObjectHasAttribute('id', $well);
        $this->assertEquals($this->well->getId(), $well->id);
        $this->assertObjectHasAttribute('name', $well);
        $this->assertEquals($this->well->getName(), $well->name);
    }

    /**
     * Test for the API-Call /api/wells/<id>.json
     */
    public function testWellDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/wells/'.$this->well->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $well = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $well);
        $this->assertEquals($this->well->getId(), $well->id);
        $this->assertObjectHasAttribute('name', $well);
        $this->assertEquals($this->well->getName(), $well->name);
        $this->assertObjectHasAttribute('point', $well);
        $point = $well->point;
        $this->assertEquals($this->well->getPoint()->getX(), $point->x);
        $this->assertEquals($this->well->getPoint()->getY(), $point->y);
        $this->assertEquals($this->well->getPoint()->getSrid(), $point->srid);
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

        $entities = $this->entityManager->getRepository('AppBundle:Well')
            ->findAll();

        foreach ($entities as $entity)
        {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
