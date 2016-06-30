<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Well;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\Point;
use AppBundle\Model\WellFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WellTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /** @var  Well */
    protected $industrialWell;

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

        $this->industrialWell = WellFactory::createIndustrialWell();
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\Well', $this->industrialWell);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->industrialWell->getId());
    }

    public function testSetGetName(){
        $this->assertNull($this->industrialWell->getName());
        $this->industrialWell->setName('IW_1');
        $this->assertEquals("IW_1", $this->industrialWell->getName());
    }

    public function testSetGetWellType(){
        $this->industrialWell->setWellType('WT_1');
        $this->assertEquals("WT_1", $this->industrialWell->getWellType());
    }

    public function testSetGetPoint(){
        $this->assertNull($this->industrialWell->getPoint());
        $point = new Point(11777056.491046, 2403440.170283, 3857);
        $this->industrialWell->setPoint($point);
        $this->assertEquals($point, $this->industrialWell->getPoint());
    }

    public function testConvertPointToPoint()
    {
        $this->assertNull($this->industrialWell->convertPointToPoint());
        $point = new Point(11777056.491046, 2403440.170283, 3857);
        $this->industrialWell->setPoint($point);
        $this->assertInstanceOf('AppBundle\Model\Point', $this->industrialWell->convertPointToPoint());
        $this->assertEquals($point->getX(), $this->industrialWell->convertPointToPoint()->getX());
        $this->assertEquals($point->getY(), $this->industrialWell->convertPointToPoint()->getY());
        $this->assertEquals($point->getSrid(), $this->industrialWell->convertPointToPoint()->getSrid());
    }

    public function testIfEntityCanBePersisted(){
        $this->industrialWell
            ->setName('IW_1')
            ->setPoint(new Point(11777056.491046, 2403440.170283, 3857));

        $this->entityManager->persist($this->industrialWell);
        $this->entityManager->flush();

        $iw = $this->entityManager->getRepository('AppBundle:Well')
            ->findOneBy(array(
                'id' => $this->industrialWell->getId()->toString()
            ));

        $this->assertTrue($iw instanceof Well);
        $this->assertEquals(Well::TYPE_INDUSTRIAL_WELL, $iw->getWellType());
        $this->entityManager->remove($iw);
        $this->entityManager->flush();
    }

    public function testGetLayerId(){
        $this->assertNull($this->industrialWell->getLayerId());
        $gl = GeologicalLayerFactory::create();
        $this->industrialWell->setLayer($gl);
        $this->assertEquals($gl->getId()->toString(), $this->industrialWell->getLayerId());
    }

    public function testSetGetLayer(){
        $this->assertNull($this->industrialWell->getLayer());
        $gl = GeologicalLayerFactory::create();
        $this->industrialWell->setLayer($gl);
        $this->assertEquals($gl, $this->industrialWell->getLayer());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }
}
