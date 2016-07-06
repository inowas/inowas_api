<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\SoilModel;
use AppBundle\Entity\User;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\UserFactory;
use AppBundle\Model\WellBoundaryFactory;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModflowModelRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var Serializer */
    protected $serializer;

    /** @var User $owner */
    protected $owner;

    /** @var  User $participant */
    protected $participant;

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
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer')
        ;

        $this->owner = UserFactory::createTestUser("ModelTest_Owner");
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->modFlowModel = ModFlowModelFactory::create();
        $this->modFlowModel->setOwner($this->owner);
        $this->modFlowModel->setName("TestModel");
        $this->modFlowModel->setDescription('TestModelDescription!!!');
        $this->modFlowModel->setPublic(true);

        $this->modFlowModel->setSoilModel(SoilModelFactory::create()
            ->setOwner($this->owner)
            ->setPublic(true)
            ->setName('SoilModel_TestCase')
            ->addGeologicalLayer(GeologicalLayerFactory::create()
                ->setOwner($this->owner)
                ->setPublic(true)
                ->setName("ModelTest_Layer")
                ->setOrder(GeologicalLayer::TOP_LAYER)
                ->addProperty(PropertyFactory::create()
                    ->setName("ModelTest_Property")
                    ->setPropertyType(PropertyTypeFactory::create()->setName("KF-X")->setAbbreviation('kx'))
                    ->addValue(PropertyValueFactory::create()->setValue(1.9991))
                )
            )
        );

        $this->modFlowModel->setBoundingBox(new BoundingBox(1.1, 2.2, 3.3, 4.4));
        $this->entityManager->persist($this->modFlowModel);
        $this->entityManager->flush();

        $this->modFlowModel->addBoundary(GeneralHeadBoundaryFactory::create()
            ->setName('GHB1')
            ->setPublic(true)
            ->setOwner($this->owner)
        );

        $this->modFlowModel->addBoundary(ConstantHeadBoundaryFactory::create()
            ->setName('CHB1')
            ->setPublic(true)
            ->setOwner($this->owner)
        );

        $this->modFlowModel->addBoundary(WellBoundaryFactory::create()
            ->setPoint(new Point(10, 11, 3857))
            ->setName('Well1')
            ->setPublic(true)
            ->setOwner($this->owner)
        );

        $this->modFlowModel->addBoundary(StreamBoundaryFactory::create()
            ->setStartingPoint(new Point(10, 11, 3857))
            ->setName('River1')
            ->setPublic(true)
            ->setOwner($this->owner)
        );

        $this->entityManager->persist($this->modFlowModel);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/models.json
     * which is providing a list of projects of the user
     */
    public function testGetListByUsername()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/models.json');
        
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
        $client->request('GET', '/api/users/unknownUser/models.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetModelDetailsById()
    {
        $client = static::createClient();
        $client->request('GET', '/api/models/'.$this->modFlowModel->getId().'.json');

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
        $client->request('GET', '/api/models/122.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetModelDetailsWithUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/models/'.Uuid::uuid4()->toString().'.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetModflowModelBoundariesById()
    {
        $client = static::createClient();
        $client->request('GET', '/api/modflowmodels/'.$this->modFlowModel->getId().'/boundaries.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $boundaries = json_decode($client->getResponse()->getContent());
        $this->assertCount(4, $boundaries);
    }

    public function testGetModflowModelConstantHeadAPI()
    {
        $client = static::createClient();
        $client->request('GET', '/api/modflowmodels/'.$this->modFlowModel->getId().'/constant_head.json');
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
        $client->request('GET', '/api/modflowmodels/'.$this->modFlowModel->getId().'/general_head.json');
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
        $client->request('GET', '/api/modflowmodels/'.$this->modFlowModel->getId().'/wells.json');
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
        $client->request('GET', '/api/modflowmodels/'.$this->modFlowModel->getId().'/rivers.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $rivers = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $rivers);
        $river = $rivers[0];
        $this->assertObjectHasAttribute('type', $river);
        $this->assertEquals('RIV', $river->type);
    }

    public function testGetModFlowModelBoundingBoxWithSridZero()
    {
        $client = static::createClient();
        $client->request('GET', '/api/modflowmodels/'.$this->modFlowModel->getId().'/boundingbox.json');
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
        $this->entityManager->persist($this->modFlowModel);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/api/modflowmodels/'.$this->modFlowModel->getId().'/boundingbox.json');
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
        $this->entityManager->persist($this->modFlowModel);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/api/modflowmodels/'.$this->modFlowModel->getId().'/boundingbox.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $bb = json_decode($client->getResponse()->getContent());
        $expectedArray = array(
            array(367557.59130077, 122451.43987260001),
            array(490287.90003313002, 244902.87974520001)
        );

        $this->assertEquals($expectedArray, $bb);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->owner->getUsername()
            ));
        $this->entityManager->remove($user);

        $model = $this->entityManager->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
               'name' => $this->modFlowModel->getName()
            ));

        $this->entityManager->remove($model);
        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
