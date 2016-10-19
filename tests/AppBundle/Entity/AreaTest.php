<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Area;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\UserFactory;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AreaTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var Area $area
     */
    protected $area;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        // Setup
        $this->user = UserFactory::createTestUser('entity_area_test');
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        // Create Area
        $this->area = AreaFactory::create();
        $this->area->setOwner($this->user);
        $this->area->setName('Test');
    }


    public function testAreaWithNoGeometryReturnsEmptyBoundingBox()
    {
        $this->assertEquals($this->area->getBoundingBox(), null);
    }

    public function testAreaWithGeometryReturnsBoundingBoxWithValues()
    {
        $this->area->setGeometry(new Polygon(array(
            array(
                array(1.1,-1.1),
                array(2.1,2.1),
                array(0.5, -1.2),
                array(4.1, 12.2),
                array(1.1,-1.1)
            )), 4326));

        $this->assertEquals($this->area->getBoundingBox(), new BoundingBox(0.5, 4.1, -1.2, 12.2, 4326));
    }

    public function testGetCalculateSurfaceAreaFromArea()
    {
        $this->area->setGeometry(new Polygon(array(
            array(
                array(11777056.491046, 2403440.170283),
                array(11777973.943604, 2403506.4981163),
                array(11780228.126983, 2402856.268207),
                array(11781703.598808, 2401713.2252019),
                array(11782192.897154, 2400859.2025428),
                array(11777056.491046, 2403440.170283)
            )), 3857));

        $this->entityManager->persist($this->area);
        $this->entityManager->flush();

        $surface = $this->entityManager->getRepository('AppBundle:Area')->getAreaSurfaceById($this->area->getId());
        $this->assertEquals(3316799.67999626, $surface);
    }

    public function testGetSurface(){
        $this->area->setSurface(123.1);
        $this->assertEquals(123.1, $this->area->getSurface());
    }

    public function testGetGeometry(){
        $polygon = new Polygon(array(
            array(
                array(1.1,-1.1),
                array(2.1,2.1),
                array(0.5, -1.2),
                array(4.1, 12.2),
                array(1.1,-1.1)
            )));

        $this->area->setGeometry($polygon);
        $this->assertEquals($polygon, $this->area->getGeometry());
    }

    public function testTransformAreaPolygonFrom3857To4326()
    {
        $this->area->setGeometry(new Polygon(array(
            array(
                array(11777056.491046, 2403440.170283),
                array(11777973.943604, 2403506.4981163),
                array(11780228.126983, 2402856.268207),
                array(11781703.598808, 2401713.2252019),
                array(11782192.897154, 2400859.2025428),
                array(11777056.491046, 2403440.170283)
            )), 3857));

        $this->entityManager->persist($this->area);
        $this->entityManager->flush();

        /** @var Polygon $polygon */
        $polygonGeoJson = $this->entityManager->getRepository('AppBundle:Area')->getAreaPolygonIn4326($this->area->getId());
        $this->assertContains('"type":"Polygon","coordinates":[[[', $polygonGeoJson);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->entityManager->remove($this->area);
        $this->entityManager->remove($this->user);
        $this->entityManager->flush();
        $this->entityManager->close();
    }
}