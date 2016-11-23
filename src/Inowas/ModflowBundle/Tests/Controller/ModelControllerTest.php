<?php

namespace Inowas\ModflowBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Service\ModflowModelManager;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModelControllerTest extends WebTestCase
{

    /** @var  EntityManager */
    protected $entityManager;

    /** @var ModflowModelManager */
    protected $modelManager;

    public function setUp()
    {
        self::bootKernel();
        $this->modelManager = static::$kernel->getContainer()
            ->get('inowas.modflow.modelmanager')
        ;

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.modflow_entity_manager')
        ;
    }

    public function testPostModel() {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/modflow/model.json',
            array('name' => 'MyModelName')
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('MyModelName', $response->name);
    }

    public function testGetModelById() {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');
        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/modflow/model/%s.json', $model->getId()->toString())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertEquals($model->getId()->toString(), $response->id);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('TestModel', $response->name);
    }

    public function testPutAllAvailableArgumentsModelById() {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');
        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf('/api/modflow/model/%s.json', $model->getId()->toString()),
            array(
                'name' => 'NewName',
                'description' => 'NewDescription',
                'start' => '2015-01-01',
                'end' => '2015-12-31',
                'gridsizeNx' => 100,
                'gridsizeNy' => 200,
                'soilmodelId' => Uuid::uuid4()
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertEquals($model->getId()->toString(), $response->id);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('NewName', $response->name);
        $this->assertObjectHasAttribute('description', $response);
        $this->assertEquals('NewDescription', $response->description);


        $model = $this->entityManager->getRepository('InowasModflowBundle:ModflowModel')
            ->findOneBy(
                array('id' => $model->getId())
            );

        $this->assertInstanceOf(ModflowModel::class, $model);
        $this->assertEquals('NewName',$model->getName());
        $this->assertEquals('NewDescription',$model->getDescription());
        $this->assertEquals(new \DateTime('2015-01-01'), $model->getStart());
        $this->assertEquals(new \DateTime('2015-12-31'), $model->getEnd());
        $this->assertEquals(new GridSize(100, 200), $model->getGridSize());
        $this->assertInstanceOf(Uuid::class, $model->getSoilmodelId());
    }

    public function testPutOnlyOneArgumentModelById() {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');
        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf('/api/modflow/model/%s.json', $model->getId()->toString()),
            array(
                'description' => 'NewDescription'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertEquals($model->getId()->toString(), $response->id);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('TestModel', $response->name);
        $this->assertObjectHasAttribute('description', $response);
        $this->assertEquals('NewDescription', $response->description);


        $model = $this->entityManager->getRepository('InowasModflowBundle:ModflowModel')
            ->findOneBy(
                array('id' => $model->getId())
            );

        $this->assertInstanceOf(ModflowModel::class, $model);
        $this->assertEquals('TestModel',$model->getName());
        $this->assertEquals('NewDescription',$model->getDescription());
        $this->assertEquals(new \DateTime('2016-01-01'), $model->getStart());
        $this->assertEquals(new \DateTime('2016-12-31'), $model->getEnd());
        $this->assertEquals(new GridSize(50, 50), $model->getGridSize());
        $this->assertNull($model->getSoilmodelId());
    }



    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $models = $this->entityManager
            ->getRepository('InowasModflowBundle:ModflowModel')
            ->findAll();

        foreach ($models as $model){
            $this->entityManager->remove($model);
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
