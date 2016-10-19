<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModflowCalculation;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\SoilModel;
use AppBundle\Entity\User;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\StressPeriodFactory;
use AppBundle\Model\WellBoundaryFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Tests\AppBundle\RestControllerTestCase;

class ModflowModelRestControllerTest extends RestControllerTestCase
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
            ->get('jms_serializer')
        ;

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

        $this->modFlowModel->setGridSize(new GridSize(3,4));
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
                    ), 4326
                )
            )
        );

        $this->modFlowModel->setBoundingBox(new BoundingBox(1.1, 2.2, 3.3, 4.4, 4326));
        $this->modFlowModel->setActiveCells(ActiveCells::fromArray(array(
            array(1,1,1,1),
            array(1,1,1,1),
            array(1,1,1,1)
        )));

        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();

        $this->modFlowModel->addBoundary(GeneralHeadBoundaryFactory::create()
            ->setName('GHB1')
            ->setGeometry(new LineString(
                array(
                    new Point(1.1, 3.3, 4326),
                    new Point(2.2, 3.3, 4326)
                )))
            ->setPublic(true)
            ->setOwner($this->getOwner())
        );
        $this->modFlowModel->addBoundary(ConstantHeadBoundaryFactory::create()
            ->setName('CHB1')
            ->setGeometry(new LineString(
                array(
                    new Point(1.1, 3.3, 4326),
                    new Point(2.2, 3.3, 4326)
                )))
            ->setPublic(true)
            ->setOwner($this->getOwner())
        );
        $this->modFlowModel->addBoundary(WellBoundaryFactory::create()
            ->setGeometry(new Point(2, 4, 4326))
            ->setName('Well1')
            ->setGeometry(new Point(1.1, 3.3, 4326))
            ->setPublic(true)
            ->setOwner($this->getOwner())
        );
        $this->modFlowModel->addBoundary(StreamBoundaryFactory::create()
            ->setStartingPoint(new Point(10, 11, 3857))
            ->setName('River1')
            ->setGeometry(new LineString(
                array(
                    new Point(1.1, 3.3, 4326),
                    new Point(2.2, 3.3, 4326)
                )))
            ->setPublic(true)
            ->setOwner($this->getOwner())
        );

        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();
    }

    public function testGetListWithoutAPIKeyReturns401()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/unknownUser/modflowmodels.json');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/users/<username>/models.json
     * which is providing a list of projects of the user
     */
    public function testGetListByUsername()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/'.$this->getOwner()->getUsername().'/modflowmodels.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $modelArray = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $modelArray);
        $modFlowModel = $modelArray[0];

        $this->assertEquals($this->modFlowModel->getId(), $modFlowModel->id);
        $this->assertEquals($this->modFlowModel->getName(), $modFlowModel->name);
        $this->assertEquals($this->modFlowModel->getDescription(), $modFlowModel->description);
        $this->assertEquals($this->modFlowModel->getPublic(), $modFlowModel->public);
    }

    public function testGetListByUnknownUsernameReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/unknownUser/modflowmodels.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetModelDetailsJsonById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $modFlowModel = json_decode($client->getResponse()->getContent());

        $this->assertEquals($this->modFlowModel->getId(), $modFlowModel->id);
        $this->assertEquals($this->modFlowModel->getName(), $modFlowModel->name);
        $this->assertEquals($this->modFlowModel->getDescription(), $modFlowModel->description);
        $this->assertObjectHasAttribute('grid_size', $modFlowModel);
        $this->assertObjectHasAttribute('n_x', $modFlowModel->grid_size);
        $this->assertEquals(3, $modFlowModel->grid_size->n_x);
        $this->assertObjectHasAttribute('n_y', $modFlowModel->grid_size);
        $this->assertEquals(4, $modFlowModel->grid_size->n_y);

        $this->assertObjectHasAttribute('bounding_box', $modFlowModel);
        $this->assertObjectHasAttribute('x_min', $modFlowModel->bounding_box);
        $this->assertEquals(1.1, $modFlowModel->bounding_box->x_min);
        $this->assertObjectHasAttribute('x_max', $modFlowModel->bounding_box);
        $this->assertEquals(2.2, $modFlowModel->bounding_box->x_max);
        $this->assertObjectHasAttribute('y_min', $modFlowModel->bounding_box);
        $this->assertEquals(3.3, $modFlowModel->bounding_box->y_min);
        $this->assertObjectHasAttribute('y_max', $modFlowModel->bounding_box);
        $this->assertEquals(4.4, $modFlowModel->bounding_box->y_max);
        $this->assertObjectHasAttribute('srid', $modFlowModel->bounding_box);
        $this->assertEquals(4326, $modFlowModel->bounding_box->srid);

        $this->assertEquals($this->modFlowModel->getOwner()->getId(), $modFlowModel->owner->id);
        $this->assertEquals($this->modFlowModel->getSoilModel()->getId(), $modFlowModel->soil_model->id);
        $this->assertCount(1, $modFlowModel->soil_model->geological_layers);
        $this->assertEquals($this->modFlowModel->getSoilModel()->getGeologicalLayers()->first()->getId(), $modFlowModel->soil_model->geological_layers[0]->id);
    }

    public function testGetModelDetailsHtmlById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'.html',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetModelDetailsJsonWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/122.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetModelDetailsHtmlWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/122.html',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetModelDetailsWithUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/models/'.Uuid::uuid4()->toString().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetModflowModelBoundariesById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/boundaries.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $boundaries = json_decode($client->getResponse()->getContent());
        $this->assertCount(4, $boundaries);
    }

    public function testGetModflowModelConstantHeadAPI()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/constant_head.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $chbs = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $chbs);
        $chb = $chbs[0];
        $this->assertObjectHasAttribute('type', $chb);
        $this->assertEquals('CHB', $chb->type);
    }

    public function testGetModflowModelGeneralHeadAPI()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/general_head.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $ghbs = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $ghbs);
        $ghb = $ghbs[0];
        $this->assertObjectHasAttribute('type', $ghb);
        $this->assertEquals('GHB', $ghb->type);
    }

    public function testGetModflowModelWellsAPI()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/wells.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $wells = json_decode($client->getResponse()->getContent());
        $wells = $wells->puw;
        $this->assertCount(1, $wells);
        $well = $wells[0];
        $this->assertObjectHasAttribute('type', $well);
        $this->assertEquals('WEL', $well->type);
    }

    public function testGetModflowModelRiversAPI()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/rivers.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $rivers = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $rivers);
        $river = $rivers[0];
        $this->assertObjectHasAttribute('type', $river);
        $this->assertEquals('RIV', $river->type);
    }

    public function testGetModFlowModelCalculationsWithoutCalculationsAPI()
    {
        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/calculations.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(0, json_decode($client->getResponse()->getContent()));
    }

    public function testGetModFlowModelCalculationsWithOneCalculationsAPI()
    {
        $calculation = new ModflowCalculation();
        $calculation->setModelId($this->modFlowModel->getId());
        $calculation->setBaseUrl('abc');
        $calculation->setDateTimeStart(new \DateTime('2016-01-01'));
        $calculation->setDateTimeEnd(new \DateTime('2016-01-02'));
        $calculation->setOutput('Output');
        $calculation->setErrorOutput('Error');

        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->persist($calculation);
        $this->getEntityManager()->flush();

        sleep(1);

        $timeNow = new \DateTime();
        $calculation = new ModflowCalculation();
        $calculation->setModelId($this->modFlowModel->getId());
        $calculation->setBaseUrl('abcde');
        $calculation->setDateTimeStart(new \DateTime('2016-01-02'));
        $calculation->setDateTimeEnd(new \DateTime('2016-01-03'));
        $calculation->setOutput('Output_2');
        $calculation->setErrorOutput('Error_2');

        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->persist($calculation);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/calculations.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $calculationResponse = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('model_id', $calculationResponse);
        $this->assertEquals($calculation->getModelId(), $calculationResponse->model_id);
        $this->assertObjectHasAttribute('base_url', $calculationResponse);
        $this->assertEquals($calculation->getBaseUrl(), $calculationResponse->base_url);
        $this->assertObjectHasAttribute('state', $calculationResponse);
        $this->assertEquals($calculation->getState(), $calculationResponse->state);
        $this->assertObjectHasAttribute('date_time_add_to_queue', $calculationResponse);
        $this->assertEquals($calculation->getDateTimeAddToQueue(), $timeNow);
        $this->assertObjectHasAttribute('date_time_start', $calculationResponse);
        $this->assertEquals($calculation->getDateTimeStart(), new \DateTime('2016-01-02'));
        $this->assertObjectHasAttribute('date_time_end', $calculationResponse);
        $this->assertEquals($calculation->getDateTimeEnd(), new \DateTime('2016-01-03'));
        $this->assertObjectHasAttribute('output', $calculationResponse);
        $this->assertEquals($calculation->getOutput(), $calculationResponse->output);
        $this->assertObjectHasAttribute('error_output', $calculationResponse);
        $this->assertEquals($calculation->getErrorOutput(), $calculationResponse->error_output);
    }

    public function testPostModFlowModelCalculationAPIRedirectsToCalculations()
    {
        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/calculations.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey()));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp(
            sprintf('/api\/modflowmodels\/%s\/calculations.json/', $this->modFlowModel->getId()->toString()),
            $client->getRequest()->getUri()
        );
    }

    public function testGetModFlowModelBoundingBoxWithSrid3857ShouldNotTransform()
    {
        $this->modFlowModel->setBoundingBox(new BoundingBox(1.1, 2.2, 3.3, 4.4, 3857));
        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/boundingbox.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $bb = json_decode($client->getResponse()->getContent());
        $expectedArray = array(
            array($this->modFlowModel->getBoundingBox()->getYMin(), $this->modFlowModel->getBoundingBox()->getXMin()),
            array($this->modFlowModel->getBoundingBox()->getYMax(), $this->modFlowModel->getBoundingBox()->getXMax())
        );
        $this->assertEquals($expectedArray, $bb);
    }

    public function testGetModFlowModelBoundingBoxWithSrid4326ShouldTransform()
    {
        $this->modFlowModel->setBoundingBox(new BoundingBox(1.1, 2.2, 3.3, 4.4, 4326));
        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/boundingbox.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $bb = json_decode($client->getResponse()->getContent());
        $expectedArray = array(
            array(367557.59130077, 122451.43987260001),
            array(490287.90003313002, 244902.87974520001)
        );

        $this->assertEquals($expectedArray, $bb);
    }

    public function testGetModFlowModelProperties(){
        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'/properties.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $properties = json_decode($client->getResponse()->getContent());

        $this->assertEquals($this->modFlowModel->getActiveCells()->toArray(), $properties->active_cells->cells);
        $this->assertEquals($this->modFlowModel->getGridSize()->getNX(), $properties->grid_size->n_x);
        $this->assertEquals($this->modFlowModel->getGridSize()->getNY(), $properties->grid_size->n_y);
        $this->assertEquals($this->modFlowModel->getBoundingBox()->getXMin(), $properties->bounding_box->x_min);
        $this->assertEquals($this->modFlowModel->getBoundingBox()->getXMax(), $properties->bounding_box->x_max);
        $this->assertEquals($this->modFlowModel->getBoundingBox()->getYMin(), $properties->bounding_box->y_min);
        $this->assertEquals($this->modFlowModel->getBoundingBox()->getYMax(), $properties->bounding_box->y_max);
        $this->assertEquals($this->modFlowModel->getBoundingBox()->getSrid(), $properties->bounding_box->srid);
    }

    public function testPostModflowModelWithValues(){
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/modflowmodels.json',
            array(
                'name' => 'MyShinyNewModel',
                'description' => 'MyModelDescription',
                'public' => false
            ),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $redirectUri = $client->getRequest()->getUri();

        $client->request(
            'GET',
            $redirectUri,
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $response = $client->getResponse()->getContent();
        $response = json_decode($response);

        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('MyShinyNewModel', $response->name);
        $this->assertObjectHasAttribute('description', $response);
        $this->assertEquals('MyModelDescription', $response->description);
        $this->assertObjectHasAttribute('public', $response);
        $this->assertFalse($response->public);

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array('id' => $this->modFlowModel->getId()->toString()));
        $this->getEntityManager()->remove($model);

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array('id' => $response->id));
        $this->getEntityManager()->remove($model);
        $this->getEntityManager()->flush();
    }

    public function testPostModflowModelWithoutValuesAppliesDefaults(){
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/modflowmodels.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $redirectUri = $client->getRequest()->getUri();

        $client->request(
            'GET',
            $redirectUri,
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $response = $client->getResponse()->getContent();
        $response = json_decode($response);

        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('', $response->name);
        $this->assertObjectHasAttribute('description', $response);
        $this->assertEquals('', $response->description);
        $this->assertObjectHasAttribute('public', $response);
        $this->assertTrue($response->public);

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array('id' => $this->modFlowModel->getId()->toString()));
        $this->getEntityManager()->remove($model);

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array('id' => $response->id));
        $this->getEntityManager()->remove($model);

        $this->getEntityManager()->flush();
    }

    public function testGetHeads(){
        $this->modFlowModel->setHeads(array(
            20 => Flopy3DArray::fromValue(1,2,3,4)->toArray()
        ));

        $this->modFlowModel->addBoundary(
            WellBoundaryFactory::create()
            ->setName('WellBoundary')
            ->addStressPeriod(StressPeriodFactory::createWel()
                ->setFlux(1000)
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('2.1.2015'))
            )
        );

        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/modflowmodels/%s/heads.json', $this->modFlowModel->getId()->toString()),
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"2015-01-20":[[[1,1,1,1],[1,1,1,1],[1,1,1,1]],[[1,1,1,1],[1,1,1,1],[1,1,1,1]]]}', $client->getResponse()->getContent());

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array('id' => $this->modFlowModel->getId()->toString()));

        $this->getEntityManager()->remove($model);
        $this->getEntityManager()->flush();
    }

    public function testPostHeads(){
        $client = static::createClient();

        $data = array(
            "totim" => 20,
            "heads" => json_encode(Flopy3DArray::fromValue(1.1,1,2,3)->toArray())
        );

        $client->request(
            'POST',
            sprintf('/api/modflowmodels/%s/heads.json', $this->modFlowModel->getId()->toString()),
            $data,
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains("OK", $client->getResponse()->getContent());

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array('id' => $this->modFlowModel->getId()->toString()));

        $this->getEntityManager()->remove($model);
        $this->getEntityManager()->flush();
    }

    public function testPutModflowModelWithActiveCellsArray(){

        $active_cells = array(
            array(3,2,1),
            array(3,2,1),
            array(3,2,1));

        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/modflowmodels/'.$this->modFlowModel->getId()->toString().'.json',
            array('active_cells' => json_encode($active_cells)),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);

        $modelProperties = json_decode($response);
        $this->assertObjectHasAttribute('grid_size', $modelProperties);
        $this->assertObjectHasAttribute('bounding_box', $modelProperties);
        $this->assertObjectHasAttribute('active_cells', $modelProperties);
        $this->assertObjectHasAttribute('cells', $modelProperties->active_cells);
        $this->assertEquals($active_cells, $modelProperties->active_cells->cells);
    }

    public function testPutModflowModelWithOutActiveCellsArray(){

        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/modflowmodels/'.$this->modFlowModel->getId()->toString().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeleteModFlowModel(){
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/modflowmodels/'.$this->modFlowModel->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Success', $client->getResponse()->getContent());

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(
                array(
                    'id' => $this->modFlowModel->getId()->toString()
                )
            );

        $this->assertNull($model);
    }

    public function testGetModelScenarios(){

        $modelScenario = ModelScenarioFactory::create($this->modFlowModel);
        $modelScenario->setName('Scenario 1');
        $modelScenario->setDescription('Description Scenario 1');
        $modelScenario->setOwner($this->getOwner());
        $this->getEntityManager()->persist($modelScenario);

        $modelScenario = ModelScenarioFactory::create($this->modFlowModel);
        $modelScenario->setName('Scenario 2');
        $modelScenario->setDescription('Description Scenario 2');
        $modelScenario->setOwner($this->getOwner());
        $this->getEntityManager()->persist($modelScenario);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/'.$this->modFlowModel->getId()->toString().'/scenarios.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertCount(2, $response);
        $scenario1 = $response[0];
        $this->assertObjectHasAttribute('id', $scenario1);
        $this->assertObjectHasAttribute('name', $scenario1);
        $this->assertObjectHasAttribute('description', $scenario1);
        $this->assertObjectHasAttribute('date_created', $scenario1);
        $this->assertObjectHasAttribute('date_modified', $scenario1);
    }

    public function testGetModelImage(){

        $client = static::createClient();
        $client->request(
            'GET',
            '/models/modflow/'.$this->modFlowModel->getId()->toString().'/image.png',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeleteModelScenario(){

        $modelScenario = ModelScenarioFactory::create($this->modFlowModel);
        $modelScenario->setName('ScenarioName');
        $modelScenario->setOwner($this->getOwner());

        $this->getEntityManager()->persist($modelScenario);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/modflowmodels/'.$modelScenario->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Success', $client->getResponse()->getContent());

        $model = $this->getEntityManager()->getRepository('AppBundle:ModflowModelScenario')
            ->findOneBy(
                array(
                    'id' => $this->modFlowModel->getId()->toString()
                )
            );

        $this->assertNull($model);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->getEntityManager()->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->getOwner()->getUsername()
            ));
        $this->getEntityManager()->remove($user);

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
               'name' => $this->modFlowModel->getName()
            ));

        if ($model instanceof ModFlowModel){
            $this->getEntityManager()->remove($model);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}