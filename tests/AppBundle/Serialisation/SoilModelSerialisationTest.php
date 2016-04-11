<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\SoilModel;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class SoilModelSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var SoilModel $soilModel */
    protected $soilModel;

    /** @var Area $area */
    protected $area;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->soilModel = SoilModelFactory::create();
        $this->soilModel->setId(11);
        $this->soilModel->setName("TestSoilModel");
        $this->soilModel->setPublic(true);
        $this->soilModel->setDescription('TestSoilModelDescription!!!');

        $owner = UserFactory::createTestUser("SoilModelTest_Owner")->setId(1);
        $this->soilModel->setOwner($owner);

        $this->area = AreaFactory::create()
            ->setId(12)
            ->setOwner($owner)
            ->setPublic(true)
            ->setAreaType(AreaTypeFactory::setName('SoilModelTestAreaType'))
            ->addProperty(PropertyFactory::create()->setId(14))
        ;

        $this->soilModel->setArea($this->area);

        $layer1 = GeologicalLayerFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setId(21)
            ->setName("SoilModel-TestLayer_1");

        $layer2 = GeologicalLayerFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setId(22)
            ->setName("SoilModel-TestLayer_2");
            
        $this->soilModel->addGeologicalLayer($layer1);
        $this->soilModel->addGeologicalLayer($layer2);

        $point1 = GeologicalPointFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setId(31)
            ->setName('SoilModel-TestPoint_1')
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime())
            ->setPoint(new Point(12,11,5432))
        ;

        $point2 = GeologicalPointFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setId(32)
            ->setName('SoilModel-TestPoint_2')
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime())
            ->setPoint(new Point(13,12,5432))
        ;

        $this->soilModel->addGeologicalPoint($point1);
        $this->soilModel->addGeologicalPoint($point2);

        $unit_1_1 = GeologicalUnitFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setId(41)
            ->setName("SoilModel-TestUnit_1_1")
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime())
            ->setTopElevation(12)
            ->setBottomElevation(10);

        $unit_1_2 = GeologicalUnitFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setId(42)
            ->setName("SoilModel-TestUnit_1_2")
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime())
            ->setTopElevation(10)
            ->setBottomElevation(8);

        $unit_2_1 = GeologicalUnitFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setId(43)
            ->setName("SoilModel-TestUnit_2_1")
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime())
            ->setTopElevation(12)
            ->setBottomElevation(9);

        $unit_2_2 = GeologicalUnitFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setId(44)
            ->setName("SoilModel-TestUnit_2_2")
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime())
            ->setTopElevation(9)
            ->setBottomElevation(6);

        $layer1->addGeologicalUnit($unit_1_1);
        $layer1->addGeologicalUnit($unit_1_2);
        $layer2->addGeologicalUnit($unit_2_1);
        $layer2->addGeologicalUnit($unit_2_2);

        $point1->addGeologicalUnit($unit_1_1);
        $point1->addGeologicalUnit($unit_1_2);
        $point2->addGeologicalUnit($unit_2_1);
        $point2->addGeologicalUnit($unit_2_2);

        $this->soilModel->addGeologicalUnit($unit_1_1);
        $this->soilModel->addGeologicalUnit($unit_1_2);
        $this->soilModel->addGeologicalUnit($unit_2_1);
        $this->soilModel->addGeologicalUnit($unit_2_2);
    }

    public function testSoilModelListSerialisation()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('soilmodellist');

        $serializedModel = $this->serializer->serialize($this->soilModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);
        var_dump($serializedModel);
    }

    public function testSoilModelDetailsSerialisation()
    {   
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('soilmodeldetails');

        $serializedModel = $this->serializer->serialize($this->soilModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);
        var_dump($serializedModel);
    }
}
