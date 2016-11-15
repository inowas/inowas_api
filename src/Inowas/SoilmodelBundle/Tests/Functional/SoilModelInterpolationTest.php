<?php

namespace Inowas\Soilmodel\Tests\Model;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Service\Interpolation;
use Inowas\Soilmodel\Model\Property;
use Inowas\Soilmodel\Model\PropertyType;
use Inowas\Soilmodel\Model\PropertyValueFactory;
use Inowas\Soilmodel\Model\Soilmodel;
use Inowas\Soilmodel\Service\SoilmodelManager;
use Inowas\SoilmodelBundle\Model\BoreHoleFactory;
use Inowas\SoilmodelBundle\Model\LayerFactory;
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
            ->get('inowas.soilmodel')
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

    public function testInterpolateSoilmodelLayer(){
        $soilmodel = $this->em->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'name' => 'MySoilModelName'
            ));

        $this->soilmodelManager->interpolate(
            $soilmodel,
            $soilmodel->getLayers()->first(),
            PropertyType::fromString(PropertyType::TOP_ELEVATION),
            array(
                Interpolation::TYPE_GAUSSIAN,
                Interpolation::TYPE_IDW,
                Interpolation::TYPE_MEAN
            )
        );
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

