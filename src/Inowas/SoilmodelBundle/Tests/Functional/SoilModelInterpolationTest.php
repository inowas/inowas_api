<?php

namespace Inowas\Soilmodel\Tests\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Model\BoundingBox;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\SoilmodelBundle\Model\Property;
use Inowas\SoilmodelBundle\Model\PropertyType;
use Inowas\SoilmodelBundle\Factory\PropertyValueFactory;
use Inowas\SoilmodelBundle\Factory\BoreHoleFactory;
use Inowas\SoilmodelBundle\Factory\LayerFactory;
use Inowas\SoilmodelBundle\Service\SoilmodelManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SoilModelInterpolationTest extends WebTestCase
{
    /** @var  EntityManager */
    protected $em;

    /** @var SoilmodelManager */
    protected $soilmodelManager;

    public function setUp(){

        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.soilmodel_entity_manager')
        ;

        $this->soilmodelManager = static::$kernel->getContainer()
            ->get('inowas.soilmodel.soilmodelmanager')
        ;

        $soilmodel = $this->soilmodelManager->create();
        $soilmodel->setName('MySoilModelName');
        $soilmodel->setBoundingBox(new BoundingBox(50, 50, 55, 55, 4326, 10, 11));
        $soilmodel->setGridSize(new GridSize(10, 20));
        $soilmodel->addBoreHole(BoreHoleFactory::create()
            ->setName('MyBoreHole')
            ->setDescription('MyBoreHoleDescription')
            ->setPoint(new Point(51, 51, 4326))
            ->addLayer(LayerFactory::create()
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::TOP_ELEVATION),
                        PropertyValueFactory::createFromValue(100)
                    )
                )
            )
        );

        $soilmodel->addBoreHole(BoreHoleFactory::create()
            ->setName('MyBoreHole')
            ->setDescription('MyBoreHoleDescription')
            ->setPoint(new Point(52, 52, 4326))
            ->addLayer(LayerFactory::create()
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::TOP_ELEVATION),
                        PropertyValueFactory::createFromValue(110)
                    )
                )
            )
        );

        $soilmodel->addBoreHole(BoreHoleFactory::create()
            ->setName('MyBoreHole')
            ->setDescription('MyBoreHoleDescription')
            ->setPoint(new Point(53, 53, 4326))
            ->addLayer(LayerFactory::create()
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::TOP_ELEVATION),
                        PropertyValueFactory::createFromValue(120)
                    )
                )
            )
        );

        $soilmodel->addBoreHole(BoreHoleFactory::create()
            ->setName('MyBoreHole')
            ->setDescription('MyBoreHoleDescription')
            ->setPoint(new Point(54, 54, 4326))
            ->addLayer(LayerFactory::create()
                ->addOrReplaceProperty(
                    new Property(
                        PropertyType::fromString(PropertyType::TOP_ELEVATION),
                        PropertyValueFactory::createFromValue(130)
                    )
                )
            )
        );

        $soilmodel->addLayer(LayerFactory::create());
        $this->em->persist($soilmodel);
        $this->em->flush();
        $this->em->clear();
    }


    public function testTrue(){
        $this->assertTrue(true);
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

