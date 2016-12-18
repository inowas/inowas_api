<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\BoundingBox;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\ModflowBundle\Service\ModflowToolManager;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModflowModelSerializerTest extends KernelTestCase {

    /** @var  ModflowToolManager */
    protected $modelManager;

    /** @var  Serializer */
    protected $serializer;

    public function setUp()
    {
        self::bootKernel();
        $this->modelManager = static::$kernel->getContainer()
            ->get('inowas.modflow.modelmanager')
        ;

        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer')
        ;
    }

    public function testSerialize(){
        $model = $this->modelManager->createModel();
        $model->setName('TestModel');
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setDescription('TestModelDescription');
        $model->setBoundingBox(new BoundingBox(1, 2, 3, 4, 4326));
        $model->setGridSize(new GridSize(50, 60));
        $model->setSoilmodelId(Uuid::uuid4());
        $model->addBoundary(BoundaryFactory::createRch());
        $this->modelManager->updateModel($model);

        $json = $this->serializer
            ->serialize($model, 'json',
                SerializationContext::create()
                    ->setGroups(array('details'))
            );

        $this->assertJson($json);
        $response = json_decode($json);
        $this->assertObjectHasAttribute('id', $response);
        $this->assertEquals($model->getId()->toString(), $response->id);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals($model->getName(), $response->name);
        $this->assertObjectHasAttribute('description', $response);
        $this->assertEquals($model->getDescription(), $response->description);
        $this->assertObjectHasAttribute('grid_size', $response);
        $this->assertObjectHasAttribute('n_x', $response->grid_size);
        $this->assertEquals($model->getGridSize()->getNX(), $response->grid_size->n_x);
        $this->assertObjectHasAttribute('n_y', $response->grid_size);
        $this->assertEquals($model->getGridSize()->getNY(), $response->grid_size->n_y);
        $this->assertObjectHasAttribute('bounding_box', $response);
        $this->assertObjectHasAttribute('x_min', $response->bounding_box);
        $this->assertEquals($model->getBoundingBox()->getXMin(), $response->bounding_box->x_min);
        $this->assertObjectHasAttribute('x_max', $response->bounding_box);
        $this->assertEquals($model->getBoundingBox()->getXMax(), $response->bounding_box->x_max);
        $this->assertObjectHasAttribute('y_min', $response->bounding_box);
        $this->assertEquals($model->getBoundingBox()->getYMin(), $response->bounding_box->y_min);
        $this->assertObjectHasAttribute('y_max', $response->bounding_box);
        $this->assertEquals($model->getBoundingBox()->getYMax(), $response->bounding_box->y_max);
        $this->assertObjectHasAttribute('srid', $response->bounding_box);
        $this->assertEquals($model->getBoundingBox()->getSrid(), $response->bounding_box->srid);
        $this->assertObjectHasAttribute('soilmodel_id', $response);
        $this->assertEquals($model->getSoilmodelId()->toString(), $response->soilmodel_id);
        $this->assertObjectHasAttribute('boundaries', $response);
        $this->assertTrue(is_array($response->boundaries));
    }
}
