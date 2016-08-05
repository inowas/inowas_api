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
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\WellBoundaryFactory;
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

        $this->modFlowModel->setGridSize(new GridSize(4,5));
        $this->modFlowModel->setBoundingBox(new BoundingBox(1.1, 2.2, 3.3, 4.4));
        $this->modFlowModel->setActiveCells(ActiveCells::fromArray(array(
            array(1,2,3),
            array(1,2,3),
            array(1,2,3)
        )));

        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();

        $this->modFlowModel->addBoundary(GeneralHeadBoundaryFactory::create()
            ->setName('GHB1')
            ->setPublic(true)
            ->setOwner($this->getOwner())
        );

        $this->modFlowModel->addBoundary(ConstantHeadBoundaryFactory::create()
            ->setName('CHB1')
            ->setPublic(true)
            ->setOwner($this->getOwner())
        );

        $this->modFlowModel->addBoundary(WellBoundaryFactory::create()
            ->setPoint(new Point(10, 11, 3857))
            ->setName('Well1')
            ->setPublic(true)
            ->setOwner($this->getOwner())
        );

        $this->modFlowModel->addBoundary(StreamBoundaryFactory::create()
            ->setStartingPoint(new Point(10, 11, 3857))
            ->setName('River1')
            ->setPublic(true)
            ->setOwner($this->getOwner())
        );

        $this->getEntityManager()->persist($this->modFlowModel);
        $this->getEntityManager()->flush();
    }

    public function testGetListWithoutAPIKeyReturns401()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/unknownUser/models.json');
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
            '/api/users/'.$this->getOwner()->getUsername().'/models.json',
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
            '/api/users/unknownUser/models.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetModelDetailsById()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/models/'.$this->modFlowModel->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $modFlowModel = json_decode($client->getResponse()->getContent());

        $this->assertEquals($this->modFlowModel->getId(), $modFlowModel->id);
        $this->assertEquals($this->modFlowModel->getName(), $modFlowModel->name);
        $this->assertEquals($this->modFlowModel->getDescription(), $modFlowModel->description);
        $this->assertEquals($this->modFlowModel->getOwner()->getId(), $modFlowModel->owner->id);
        $this->assertCount(0, $modFlowModel->calculation_properties->stress_periods);
        $this->assertEquals($this->modFlowModel->getSoilModel()->getId(), $modFlowModel->soil_model->id);
        $this->assertCount(1, $modFlowModel->soil_model->geological_layers);
        $this->assertEquals($this->modFlowModel->getSoilModel()->getGeologicalLayers()->first()->getId(), $modFlowModel->soil_model->geological_layers[0]->id);
    }

    public function testGetModelDetailsWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/models/122.json',
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
        $wells = $wells->cw;
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
        $timeNow = new \DateTime();
        $calculation = new ModflowCalculation();
        $calculation->setModelId($this->modFlowModel->getId());
        $calculation->setExecutable('mf2005');
        $calculation->setDateTimeStart(new \DateTime('2016-01-01'));
        $calculation->setDateTimeEnd(new \DateTime('2016-01-02'));
        $calculation->setOutput('Output');
        $calculation->setErrorOutput('Error');

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
        $calculations = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, count($calculations));
        $calculationResponse = $calculations[0];

        $this->assertObjectHasAttribute('model_id', $calculationResponse);
        $this->assertEquals($calculation->getModelId(), $calculationResponse->model_id);
        $this->assertObjectHasAttribute('executable', $calculationResponse);
        $this->assertEquals($calculation->getExecutable(), $calculationResponse->executable);
        $this->assertObjectHasAttribute('state', $calculationResponse);
        $this->assertEquals($calculation->getState(), $calculationResponse->state);
        $this->assertObjectHasAttribute('date_time_add_to_queue', $calculationResponse);
        $this->assertEquals($calculation->getDateTimeAddToQueue(), $timeNow);
        $this->assertObjectHasAttribute('date_time_start', $calculationResponse);
        $this->assertEquals($calculation->getDateTimeStart(), new \DateTime('2016-01-01'));
        $this->assertObjectHasAttribute('date_time_end', $calculationResponse);
        $this->assertEquals($calculation->getDateTimeEnd(), new \DateTime('2016-01-02'));
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
        $this->assertRegExp('/api\/calculations\//', $client->getRequest()->getUri());
    }

    public function testGetModFlowModelBoundingBoxWithSridZero()
    {
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
