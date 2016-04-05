<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Boundary;
use AppBundle\Entity\User;
use AppBundle\Model\BoundaryFactory;
use AppBundle\Model\Point;
use AppBundle\Model\UserFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoundaryRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var  Boundary $boundary */
    protected $boundary;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser('BoundaryOwner');
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->boundary = BoundaryFactory::create()
            ->setId(12)
            ->setName('Boundary')
            ->setPublic(true)
            ->setOwner($this->owner)
            ->setGeometry(new LineString(
                array(
                    new Point(11777056.49104572273790836, 2403440.17028302047401667),
                    new Point(11777973.9436037577688694, 2403506.49811625294387341),
                    new Point(11780228.12698311358690262, 2402856.2682070448063314),
                    new Point(11781703.59880801662802696, 2401713.22520185634493828)
                )
            ));

        $this->entityManager->persist($this->boundary);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/boundaries.json
     * which is providing a list of areas of the user
     */
    public function testBoundaryList()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/boundaries.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $boundaries = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, count($boundaries));
        $this->assertEquals($this->boundary->getName(), $boundaries[0]->name);
    }

    /**
     * Test for the API-Call /api/boundaries/<id>.json
     * which is providing a the details of a specific areas of the user
     */
    public function testBoundaryDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/boundaries/'.$this->boundary->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $area = json_decode($client->getResponse()->getContent());
        $this->assertEquals($area->id, $this->boundary->getId());
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

        $boundaries = $this->entityManager->getRepository('AppBundle:Boundary')
            ->findAll();

        foreach ($boundaries as $boundary)
        {
            $this->entityManager->remove($boundary);
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
