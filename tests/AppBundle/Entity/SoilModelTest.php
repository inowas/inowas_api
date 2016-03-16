<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\SoilModel;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\UserFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SoilModelTest extends WebTestCase
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
     * @var SoilModel $soilModel
     */
    protected $soilModel;

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
        $this->user = UserFactory::createTestUser('soilModel');
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        // Create SoilModel
        $this->soilModel = SoilModelFactory::create();
        $this->soilModel->setOwner($this->user);
        $this->soilModel->setName('Test');

    }

    public function testTrue()
    {
        $this->assertTrue(true);
    }

    public function testIfSoilModelIsPersistedInDatabase()
    {
        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')
            ->findAll();

        $this->assertCount(0, $soilModels);

        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();

        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')
            ->findAll();

        $this->assertCount(1, $soilModels);

        $this->entityManager->remove($this->soilModel);
        $this->entityManager->flush();

        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')
            ->findAll();

        $this->assertCount(0, $soilModels);
    }

    public function testIfLayersCanBeSetAndRetrievedFromSoilModel()
    {
        $layer1 = GeologicalLayerFactory::create();
        $layer1->setPublic(true);
        $layer1->setOwner($this->user);
        $layer1->setName('TestLayer1');
        $this->entityManager->persist($layer1);
        $this->entityManager->flush();

        $geologicalLayers = $this->entityManager->getRepository('AppBundle:GeologicalLayer')->findAll();
        $this->assertCount(1, $geologicalLayers);

        $this->soilModel->addGeologicalLayer($layer1);
        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();

        /** @var array */
        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')->findAll();
        $this->assertCount(1, $soilModels);

        /** @var ArrayCollection $layers */
        $layers = $soilModels[0]->getGeologicalLayers();
        $this->assertCount(1, $layers);
        $layer = $layers->first();
        $this->assertEquals($layer, $layer1);

        $this->entityManager->remove($this->soilModel);
        $this->entityManager->remove($layer1);
        $this->entityManager->flush();
    }

    public function testIfPointsCanBeSetAndRetrievedFromSoilModel()
    {
        $point1 = GeologicalPointFactory::create();
        $point1->setPublic(true);
        $point1->setOwner($this->user);
        $point1->setName('TestPoint1');
        $this->entityManager->persist($point1);
        $this->entityManager->flush();
        $this->entityManager->clear($point1);

        $geologicalPoints = $this->entityManager->getRepository('AppBundle:GeologicalPoint')->findAll();
        $this->assertCount(1, $geologicalPoints);

        $this->soilModel->addGeologicalPoint($point1);
        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();
        $this->entityManager->clear($this->soilModel);

        /** @var array */
        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')->findAll();
        $this->assertCount(1, $soilModels);
        $this->soilModel = $soilModels[0];

        /** @var ArrayCollection $layers */
        $points = $this->soilModel->getGeologicalPoints();
        $this->assertCount(1, $points);
        $point = $points->first();
        $this->assertEquals($point, $point1);

        $this->entityManager->remove($this->soilModel);
        $this->entityManager->remove($point1);
        $this->entityManager->flush();
    }

    public function testIfUnitsCanBeSetAndRetrievedFromSoilModel()
    {
        $unit1 = GeologicalUnitFactory::create();
        $unit1->setPublic(true);
        $unit1->setOwner($this->user);
        $unit1->setName('TestUnit1');
        $this->entityManager->persist($unit1);
        $this->entityManager->flush();
        $this->entityManager->clear($unit1);

        $geologicalUnits = $this->entityManager->getRepository('AppBundle:GeologicalUnit')->findAll();
        $this->assertCount(1, $geologicalUnits);

        $this->soilModel->addGeologicalUnit($unit1);
        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();
        $this->entityManager->clear($this->soilModel);

        /** @var array */
        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')->findAll();
        $this->assertCount(1, $soilModels);
        $this->soilModel = $soilModels[0];

        /** @var ArrayCollection $layers */
        $units = $this->soilModel->getGeologicalUnits();
        $this->assertCount(1, $units);
        $unit = $units->first();
        $this->assertEquals($unit, $unit);

        $this->entityManager->remove($this->soilModel);
        $this->entityManager->remove($unit1);
        $this->entityManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->entityManager->remove($this->user);
        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
