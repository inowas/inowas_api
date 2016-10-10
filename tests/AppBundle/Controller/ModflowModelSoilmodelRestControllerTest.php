<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\SoilModel;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Tests\AppBundle\RestControllerTestCase;

class ModflowModelSoilmodelRestControllerTest extends RestControllerTestCase
{
    /** @var Serializer */
    protected $serializer;

    /** @var ModFlowModel $modFlowModel */
    protected $modFlowModel;

    /** @var SoilModel $soilModel */
    protected $soilModel;

    /** @var  GeologicalLayer $layer */
    protected $layer;

    /** @var Property */
    protected $property;

    /** @var PropertyType */
    protected $propertyType;

    /** @var PropertyValue */
    protected $propertyValue;


    public function setUp()
    {
        self::bootKernel();
        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer');

        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->flush();

        $this->modFlowModel = ModFlowModelFactory::create();
        $this->modFlowModel->setOwner($this->getOwner());
        $this->modFlowModel->setName("TestModel");
        $this->modFlowModel->setDescription('TestModelDescription!!!');
        $this->modFlowModel->setPublic(true);
        $this->modFlowModel->setCalculationProperties(new FlopyCalculationProperties());
        $this->modFlowModel->setSoilModel(SoilModelFactory::create()
            ->setOwner($this->getOwner())
            ->setPublic(true)
            ->setName('SoilModel_TestCase')
            ->addGeologicalLayer(GeologicalLayerFactory::create()
                ->setOwner($this->getOwner())
                ->setPublic(true)
                ->setName("ModelTest_Layer")
                ->setOrder(GeologicalLayer::TOP_LAYER)
                ->addProperty(PropertyFactory::create()
                    ->setName("ModelTest_Property")
                    ->setPropertyType(PropertyTypeFactory::create(PropertyType::KX))
                    ->addValue(PropertyValueFactory::create()->setValue(1.9991))
                )
            )
        );

        $this->modFlowModel->setGridSize(new GridSize(3, 4));
        $this->modFlowModel->setArea(
            AreaFactory::create()
                ->setOwner($this->getOwner())
                ->setGeometry(new Polygon(
                        array(
                            array(
                                new Point(1.1, 3.3, 4326),
                                new Point(2.2, 3.3, 4326),
                                new Point(2.2, 4.4, 4326),
                                new Point(1.1, 4.4, 4326),
                                new Point(1.1, 3.3, 4326)
                            )
                        )
                    )
                )
        );

        $this->modFlowModel->setBoundingBox(new BoundingBox(1.1, 2.2, 3.3, 4.4, 4326));
        $this->modFlowModel->setActiveCells(ActiveCells::fromArray(array(
            array(1, 1, 1, 1),
            array(1, 1, 1, 1),
            array(1, 1, 1, 1)
        )));

        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();
    }

    public function testGetModflowModelSoilmodelDetailsJsonById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/soilmodel.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = json_decode($client->getResponse()->getContent());
        $soilModel = $this->modFlowModel->getSoilModel();

        $this->assertEquals($soilModel->getId(), $response->id);
        $this->assertEquals($soilModel->getName(), $response->name);
        $this->assertEquals($soilModel->getDescription(), $response->description);

    }

    public function testGetModflowModelSoilmodelDetailsHtmlById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/soilmodel.html',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains("<h2>Geological Layers</h2>", $client->getResponse()->getContent());
    }
}
