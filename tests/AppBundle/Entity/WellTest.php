<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Well;
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

        // Create IndustrialWell
        $this->industrialWell = WellFactory::createIndustrialWell()
            ->setName('IW_1')
            ->setPoint(new Point(11777056.491046, 2403440.170283, 3857))
        ;
        $this->entityManager->persist($this->industrialWell);
        $this->entityManager->flush();
    }

    public function testIfEntityIsPersisted(){
        $iw = $this->entityManager->getRepository('AppBundle:Well')
            ->findOneBy(array(
                'id' => $this->industrialWell->getId()->toString()
            ));

        $this->assertTrue($iw instanceof Well);
        $this->assertEquals(Well::TYPE_INDUSTRIAL_WELL, $iw->getWellType());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $iw = $this->entityManager->getRepository('AppBundle:Well')
            ->findOneBy(array(
                'id' => $this->industrialWell->getId()->toString()
            ));

        $this->entityManager->remove($iw);
        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
