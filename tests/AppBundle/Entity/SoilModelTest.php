<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\SoilModel;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
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

    public function testHasGeologicalLayers(){
        $this->assertFalse($this->soilModel->hasGeologicalLayers());
    }

    public function testGetLayerByNumberReturnsNullIfSoilModelHasNoLayers(){
        $this->assertNull($this->soilModel->getLayerByNumber(1));
    }

    public function testGetLayerByNumberReturnsNullIfLayerNumberIsNotThere(){
        $this->soilModel->addGeologicalLayer(GeologicalLayerFactory::create()->setOrder(0));
        $this->assertNull($this->soilModel->getLayerByNumber(1));
    }

    public function testGetLayerByNumberReturnsLayer(){
        $this->soilModel->addGeologicalLayer(GeologicalLayerFactory::create()->setOrder(0));
        $this->assertInstanceOf(GeologicalLayer::class, $this->soilModel->getLayerByNumber(0));
    }

    public function testPostLoad(){
        $geologicalPoint = GeologicalPointFactory::create();
        $this->soilModel->addModelObject($geologicalPoint);
        $this->assertCount(1, $this->soilModel->getModelObjects());
        $this->soilModel->postLoad();
        $this->assertCount(0, $this->soilModel->getModelObjects());
        $this->assertEquals($geologicalPoint, $this->soilModel->getGeologicalPoints()->first());

        $geologicalUnit = GeologicalUnitFactory::create();
        $this->soilModel->addModelObject($geologicalUnit);
        $this->assertCount(1, $this->soilModel->getModelObjects());
        $this->soilModel->postLoad();
        $this->assertCount(0, $this->soilModel->getModelObjects());
        $this->assertEquals($geologicalUnit, $this->soilModel->getGeologicalUnits()->first());

        $area = AreaFactory::create();
        $this->soilModel->addModelObject($area);
        $this->assertCount(1, $this->soilModel->getModelObjects());
        $this->soilModel->postLoad();
        $this->assertCount(0, $this->soilModel->getModelObjects());
        $this->assertEquals($area, $this->soilModel->getArea());
    }

    public function testIfSoilModelCanBePersistedInDatabase()
    {
        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')
            ->findBy(array(
                 'owner' => $this->user
                )
            );

        $this->assertCount(0, $soilModels);
        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();

        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')
            ->findBy(array(
                    'owner' => $this->user
                )
            );

        $this->assertCount(1, $soilModels);

        $this->entityManager->remove($this->soilModel);
        $this->entityManager->flush();

        $soilModels = $this->entityManager->getRepository('AppBundle:SoilModel')
            ->findBy(array(
                    'owner' => $this->user
                )
            );
        $this->assertCount(0, $soilModels);
    }

    public function testIfLayersCanBeAddedAndRetrievedFromSoilModel()
    {
        $this->assertEquals(0, $this->soilModel->getNumberOfGeologicalLayers());
        $this->soilModel->addGeologicalLayer(GeologicalLayerFactory::create()
            ->setPublic(true)
            ->setOwner($this->user)
            ->setName('TestLayer 1')
            ->setOrder(GeologicalLayer::TOP_LAYER));
        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();
        $this->entityManager->clear($this->soilModel);

        /** @var array */
        $soilModels = $this->entityManager
            ->getRepository('AppBundle:SoilModel')
            ->findBy(array(
                'owner' => $this->user
            ));
        
        $this->assertCount(1, $soilModels);
        $this->soilModel = $soilModels[0];

        /** @var ArrayCollection $layers */
        $layers = $this->soilModel->getGeologicalLayers();
        $this->assertCount(1, $layers);
        $this->assertEquals(1, $this->soilModel->getNumberOfGeologicalLayers());

        /** @var GeologicalLayer $layer */
        $layer = $layers->first();
        $this->assertEquals('TestLayer 1', $layer->getName());

        $this->entityManager->remove($this->soilModel);
        $this->entityManager->flush();
    }

    public function testIfPointsCanBeAddedAndRetrievedFromSoilModel()
    {
        $this->soilModel->addGeologicalPoint(GeologicalPointFactory::create()
            ->setPublic(true)
            ->setOwner($this->user)
            ->setName('TestPoint 1'));

        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();
        $this->entityManager->clear($this->soilModel);

        /** @var array */
        $soilModels = $this->entityManager
            ->getRepository('AppBundle:SoilModel')
            ->findBy(array(
                'owner' => $this->user
            ));

        $this->assertCount(1, $soilModels);
        $this->soilModel = $soilModels[0];

        /** @var ArrayCollection $layers */
        $points = $this->soilModel->getGeologicalPoints();
        $this->assertCount(1, $points);

        /** @var GeologicalPoint $point */
        $point = $points->first();
        $this->assertEquals('TestPoint 1', $point->getName());
        $this->entityManager->remove($this->soilModel);
        $this->entityManager->flush();
    }

    public function testIfSoilModelCanBeSaved()
    {
        $this->soilModel->addGeologicalUnit(GeologicalUnitFactory::create()
            ->setPublic(true)
            ->setOwner($this->user)
            ->setName('TestUnit 1')
            ->setOrder(GeologicalUnit::TOP_LAYER));

        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();
    }

    public function testIfUnitsCanBeSetAndRetrievedFromSoilModel()
    {
        $this->soilModel->addGeologicalUnit(GeologicalUnitFactory::create()
            ->setPublic(true)
            ->setOwner($this->user)
            ->setName('TestUnit 1')
            ->setOrder(GeologicalUnit::TOP_LAYER));

        $this->entityManager->persist($this->soilModel);
        $this->entityManager->flush();
        $this->entityManager->clear($this->soilModel);

        /** @var array */
        $soilModels = $this->entityManager
            ->getRepository('AppBundle:SoilModel')
            ->findBy(array(
                'owner' => $this->user
            ));
        $this->assertCount(1, $soilModels);
        $this->soilModel = $soilModels[0];

        /** @var ArrayCollection $layers */
        $units = $this->soilModel->getGeologicalUnits();
        $this->assertCount(1, $units);

        /** @var GeologicalUnit $unit */
        $unit = $units->first();
        $this->assertEquals('TestUnit 1', $unit->getName());

        $this->entityManager->remove($this->soilModel);
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
