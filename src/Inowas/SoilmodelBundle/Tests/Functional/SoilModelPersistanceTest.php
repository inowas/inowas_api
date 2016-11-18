<?php

namespace Inowas\Soilmodel\Tests\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\EntityManager;
use Inowas\SoilmodelBundle\Model\BoreHole;
use Inowas\SoilmodelBundle\Model\Layer;
use Inowas\SoilmodelBundle\Model\Property;
use Inowas\SoilmodelBundle\Model\PropertyType;
use Inowas\SoilmodelBundle\Model\PropertyValue;
use Inowas\SoilmodelBundle\Factory\PropertyValueFactory;
use Inowas\SoilmodelBundle\Model\Soilmodel;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;
use Inowas\SoilmodelBundle\Factory\BoreHoleFactory;
use Inowas\SoilmodelBundle\Factory\LayerFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SoilModelPersistenceTest extends WebTestCase
{
    /** @var  EntityManager */
    protected $em;

    public function setUp(){

        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.soilmodel_entity_manager')
        ;

        $soilmodel = SoilmodelFactory::create();
        $soilmodel->setName('MySoilModelName');
        $soilmodel->setBoundingBox(new BoundingBox(1,2,3,4, 4321, 10, 11));
        $soilmodel->setGridSize(new GridSize(10, 12));
        $soilmodel->addBoreHole(
            BoreHoleFactory::create()
                ->setName('MyBoreHole')
                ->setDescription('MyBoreHoleDescription')
                ->setPoint(new Point(50, 50, 4326))
                ->addLayer(LayerFactory::create()
                    ->addOrReplaceProperty(
                        new Property(
                            PropertyType::fromString(PropertyType::K_X),
                            PropertyValueFactory::createFromValue(1.1)
                        )
                    )
                )
                ->addLayer(LayerFactory::create())
        );
        $soilmodel->addBoreHole(
            BoreHoleFactory::create()
                ->setPoint(new Point(50, 50, 4326))
                ->addLayer(LayerFactory::create())
                ->addLayer(LayerFactory::create())
        );
        $soilmodel->addLayer(
            LayerFactory::create()
            ->setName('MyLayer')
            ->setDescription('MyLayerDescription')
            ->addOrReplaceProperty(
                new Property(
                    PropertyType::fromString(PropertyType::BOTTOM_ELEVATION),
                    PropertyValueFactory::createFromValue(array([1,2,3],[1,2,3]))
                )
            )
        );
        $soilmodel->addLayer(LayerFactory::create());
        $this->em->persist($soilmodel);
        $this->em->flush();
        $this->em->clear();
    }

    public function testSoilModelWillBePersistedWithName(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertInstanceOf(Soilmodel::class, $soilModel);
        $this->assertEquals('MySoilModelName', $soilModel->getName());
    }

    public function testSoilModelWillBePersistedWithBoundingBox(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertInstanceOf(BoundingBox::class, $soilModel->getBoundingBox());
        $this->assertEquals(new BoundingBox(1,2,3,4, 4321, 10, 11), $soilModel->getBoundingBox());
    }

    public function testSoilModelWillBePersistedWithGridSize(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertInstanceOf(GridSize::class, $soilModel->getGridSize());
        $this->assertEquals(new GridSize(10, 12), $soilModel->getGridSize());
    }

    public function testSoilModelWillBePersistedWithBoreholes(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertCount(2, $soilModel->getBoreHoles());

        /** @var BoreHole $firstBoreHole */
        $firstBoreHole = $soilModel->getBoreHoles()->first();
        $this->assertEquals('MyBoreHole', $firstBoreHole->getName());
        $this->assertEquals('MyBoreHoleDescription', $firstBoreHole->getDescription());

        $this->assertCount(2, $firstBoreHole->getLayers());

        /** @var Layer $firstLayer */
        $firstLayer = $firstBoreHole->getLayers()->first();
        $this->assertCount(1, $firstLayer->getProperties());

        /** @var Property $property */
        $property = $firstLayer->getProperties()->first();

        /** @var PropertyType $propertyType */
        $propertyType = $property->getType();
        $this->assertInstanceOf(PropertyType::class, $propertyType);
        $this->assertEquals('kx', $propertyType->getType());

        /** @var PropertyValue $propertyValue */
        $propertyValue = $property->getValue();
        $this->assertEquals(1.1, $propertyValue->getValue());
    }

    public function testRemoveOneBoreHoleMaintainsSoilModel(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $soilModel->removeBoreHole($soilModel->getBoreHoles()->first());
        $this->em->persist($soilModel);
        $this->em->flush();
        $this->em->clear();

        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertInstanceOf(Soilmodel::class, $soilModel);
        $this->assertCount(1, $soilModel->getBoreHoles());
    }

    public function testSoilModelWillBePersistedWithBoreholesAndPoint(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertCount(2, $soilModel->getBoreHoles());
        $this->assertInstanceOf(Point::class, $soilModel->getBoreHoles()->first()->getPoint());
    }

    public function testSoilModelWillBePersistedWithBoreholesAndLayers(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertInstanceOf(Layer::class, $soilModel->getBoreHoles()->first()->getLayers()->first());

        /** @var BoreHole $boreHole */
        $boreHole = $soilModel->getBoreHoles()->first();

        /** @var Layer $secondLayer */
        $secondLayer = $boreHole->getLayers()->last();
        $this->assertEquals(1, $secondLayer->getOrder());
    }

    public function testSoilModelWillBePersistedWithLayersAndNameDescription(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertCount(2, $soilModel->getLayers());

        /** @var Layer $firstLayer */
        $firstLayer = $soilModel->getLayers()->first();
        $this->assertEquals(0, $firstLayer->getOrder());
        $this->assertEquals('MyLayer', $firstLayer->getName());
        $this->assertEquals('MyLayerDescription', $firstLayer->getDescription());

        $this->assertCount(1, $firstLayer->getProperties());

        /** @var Property $property */
        $property = $firstLayer->getProperties()->first();
        $this->assertInstanceOf(PropertyValue::class, $property->getValue());

        /** @var PropertyValue $propertyValue */
        $propertyValue = $property->getValue();
        $this->assertEquals(array([1,2,3],[1,2,3]), $propertyValue->getValue());

        $nextLayer = $soilModel->getLayers()->next();
        $this->assertEquals(1, $nextLayer->getOrder());
    }

    public function testRemoveLayerMaintainsSoilmodel(){
        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $soilModel->removeLayer($soilModel->getLayers()->first());
        $this->em->persist($soilModel);
        $this->em->flush();
        $this->em->clear();

        $soilModel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->assertCount(1, $soilModel->getLayers());
    }

    public function tearDown(){
        $soilmodels = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findAll();

        foreach ($soilmodels as $soilmodel){
            $this->em->remove($soilmodel);
        }

        $this->em->flush();
    }
}

