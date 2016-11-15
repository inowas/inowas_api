<?php

namespace Inowas\SoilmodelBundle\Tests\Service;

use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Service\Interpolation;
use Inowas\Soilmodel\Model\Property;
use Inowas\Soilmodel\Model\PropertyType;
use Inowas\Soilmodel\Model\PropertyValue;
use Inowas\Soilmodel\Model\Soilmodel;
use Inowas\Soilmodel\Factory\SoilmodelFactory;
use Inowas\Soilmodel\Service\SoilmodelManager;
use Inowas\Soilmodel\Factory\BoreHoleFactory;
use Inowas\Soilmodel\Factory\LayerFactory;

class SoilmodelManagerTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $entityManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $interpolationMock;

    /** @var  SoilmodelManager */
    private $soilmodelManager;

    public function setUp(){

        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['persist', 'flush'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $this->interpolationMock = $this->getMockBuilder(Interpolation::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();
    }

    public function testCreateSoilmodel(){
        $this->soilmodelManager = new SoilmodelManager($this->entityManagerMock, $this->interpolationMock);
        $this->assertInstanceOf(Soilmodel::class, $this->soilmodelManager->create());
    }

    public function testUpdatePersistsSoilmodel(){
        $soilmodel = SoilmodelFactory::create();;
        $this->entityManagerMock->expects($this->once())->method('persist')->with($this->equalTo($soilmodel));
        $this->soilmodelManager = new SoilmodelManager($this->entityManagerMock, $this->interpolationMock);
        $this->soilmodelManager->update($soilmodel);
    }

    public function testUpdateFlushesSoilmodel(){
        $soilmodel = SoilmodelFactory::create();;
        $this->entityManagerMock->expects($this->once())->method('flush');
        $this->soilmodelManager = new SoilmodelManager($this->entityManagerMock, $this->interpolationMock);
        $this->soilmodelManager->update($soilmodel);
    }

    public function testReadPropertyTypesFrom(){
        $soilmodel = SoilmodelFactory::create();
        $soilmodel->addLayer(LayerFactory::create())
            ->addLayer(LayerFactory::create())
            ->addLayer(LayerFactory::create())
            ->addBoreHole(BoreHoleFactory::create()
            ->addLayer(LayerFactory::create()
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::BOTTOM_ELEVATION),
                        PropertyValue::fromValue(1.1)
                    )
                )
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::TOP_ELEVATION),
                        PropertyValue::fromValue(1.1)
                    )
                )
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::HYDRAULIC_CONDUCTIVITY),
                        PropertyValue::fromValue(1.1)
                    )
                )
            )
            ->addLayer(LayerFactory::create()
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::BOTTOM_ELEVATION),
                        PropertyValue::fromValue(1.1)
                    )
                )
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::TOP_ELEVATION),
                        PropertyValue::fromValue(1.1)
                    )
                )
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::K_Z),
                        PropertyValue::fromValue(1.1)
                    )
                )
            )
            ->addLayer(LayerFactory::create()
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::BOTTOM_ELEVATION),
                        PropertyValue::fromValue(1.1)
                    )
                )
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::TOP_ELEVATION),
                        PropertyValue::fromValue(1.1)
                    )
                )
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::SPECIFIC_YIELD),
                        PropertyValue::fromValue(1.1)
                    )
                )
            )
        );
        $this->soilmodelManager = new SoilmodelManager($this->entityManagerMock, $this->interpolationMock);

        $layers = $soilmodel->getLayers();

        $propertyTypes = $this->soilmodelManager->readPropertyTypesFrom($soilmodel, $layers->first());
        $this->assertCount(3, $propertyTypes);
        $this->assertEquals(PropertyType::fromString(PropertyType::BOTTOM_ELEVATION), $propertyTypes[0]);
        $this->assertEquals(PropertyType::fromString(PropertyType::TOP_ELEVATION), $propertyTypes[1]);
        $this->assertEquals(PropertyType::fromString(PropertyType::HYDRAULIC_CONDUCTIVITY), $propertyTypes[2]);

        $propertyTypes = $this->soilmodelManager->readPropertyTypesFrom($soilmodel, $layers->next());
        $this->assertCount(3, $propertyTypes);
        $this->assertEquals(PropertyType::fromString(PropertyType::BOTTOM_ELEVATION), $propertyTypes[0]);
        $this->assertEquals(PropertyType::fromString(PropertyType::TOP_ELEVATION), $propertyTypes[1]);
        $this->assertEquals(PropertyType::fromString(PropertyType::K_Z), $propertyTypes[2]);

        $propertyTypes = $this->soilmodelManager->readPropertyTypesFrom($soilmodel, $layers->next());
        $this->assertCount(3, $propertyTypes);
        $this->assertEquals(PropertyType::fromString(PropertyType::BOTTOM_ELEVATION), $propertyTypes[0]);
        $this->assertEquals(PropertyType::fromString(PropertyType::TOP_ELEVATION), $propertyTypes[1]);
        $this->assertEquals(PropertyType::fromString(PropertyType::SPECIFIC_YIELD), $propertyTypes[2]);
    }

    public function tearDown()
    {
        unset($this->entityManagerMock);
        unset($this->interpolationMock);
        unset($this->soilmodelManager);
    }
}
