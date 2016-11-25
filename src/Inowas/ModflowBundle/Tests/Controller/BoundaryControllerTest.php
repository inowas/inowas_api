<?php

namespace Inowas\ModflowBundle\Tests\Controller;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Service\ModflowModelManager;
use Inowas\SoilmodelBundle\Service\SoilmodelManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoundaryControllerTest extends WebTestCase
{

    /** @var  EntityManager */
    protected $entityManager;

    /** @var ModflowModelManager */
    protected $modelManager;

    /** @var SoilmodelManager */
    protected $soilModelManager;

    public function setUp()
    {
        self::bootKernel();
        $this->modelManager = static::$kernel->getContainer()
            ->get('inowas.modflow.modelmanager')
        ;

        $this->soilModelManager = static::$kernel->getContainer()
            ->get('inowas.soilmodel.soilmodelmanager')
        ;

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.modflow_entity_manager')
        ;
    }

    public function testPostBoundary()
    {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');
        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/modflow/models/%s/boundaries.json', $model->getId()->toString()),
            array(
                'name' => 'MyBoundary',
                'type' => 'chd'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('MyBoundary', $response->name);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('chd', strtolower($response->type));
        $model = $this->modelManager->findById($model->getId());
        $this->assertCount(1, $model->getBoundaries());
    }

    public function testGetBoundariesByModelId()
    {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyBoundary_1');

        $model->addBoundary($boundary);

        $boundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyBoundary_2');

        $model->addBoundary($boundary);

        $boundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyBoundary_3');

        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/models/%s/boundaries.json',
                $model->getId()->toString()
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());#
        $this->assertCount(3, $response);
    }

    public function testGetBoundariesByModelIdAndType()
    {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('ChdBoundary');
        $model->addBoundary($boundary);

        $boundary = BoundaryFactory::createGhb()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('GhbBoundary');
        $model->addBoundary($boundary);

        $boundary = BoundaryFactory::createRch()
            ->setName('RchBoundary');
        $model->addBoundary($boundary);

        $boundary = BoundaryFactory::createRiv()
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('RivBoundary');
        $model->addBoundary($boundary);

        $boundary = BoundaryFactory::createWel()
            ->setGeometry(new Point(1,2,3))
            ->setName('WelBoundary');
        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/models/%s/boundaries/chd.json',
                $model->getId()->toString()
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $response);
        $this->assertEquals('ChdBoundary', $response[0]->name);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/models/%s/boundaries/ghb.json',
                $model->getId()->toString()
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $response);
        $this->assertEquals('GhbBoundary', $response[0]->name);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/models/%s/boundaries/rch.json',
                $model->getId()->toString()
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $response);
        $this->assertEquals('RchBoundary', $response[0]->name);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/models/%s/boundaries/riv.json',
                $model->getId()->toString()
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $response);
        $this->assertEquals('RivBoundary', $response[0]->name);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/models/%s/boundaries/wel.json',
                $model->getId()->toString()
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertCount(1, $response);
        $this->assertEquals('WelBoundary', $response[0]->name);
    }

    public function testGetChdBoundaryDetailsByBoundaryId()
    {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyBoundary');

        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('MyBoundary', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('chd', strtolower($response->type));
        $this->assertObjectHasAttribute('layer_numbers', $response);
        $this->assertEquals(array(1,2,3), $response->layer_numbers);
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"LineString","coordinates":[[0,0],[1,1]]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);

        $model = $this->modelManager->findById($model->getId());
        $this->assertCount(1, $model->getBoundaries());
    }

    public function testGetGhbBoundaryDetailsByBoundaryId()
    {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createGhb()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyGhbBoundary');

        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('MyGhbBoundary', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('ghb', strtolower($response->type));
        $this->assertObjectHasAttribute('layer_numbers', $response);
        $this->assertEquals(array(1,2,3), $response->layer_numbers);
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"LineString","coordinates":[[0,0],[1,1]]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);

        $model = $this->modelManager->findById($model->getId());
        $this->assertCount(1, $model->getBoundaries());
    }

    public function testGetRchBoundaryDetailsByBoundaryId()
    {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createRch()
            ->setGeometry(new Polygon(array(
                [
                    new Point(0,0),
                    new Point(0,1),
                    new Point(1,1),
                    new Point(1,0),
                    new Point(0,0)
                ]
            )))
            ->setName('MyRchBoundary');

        $model->addBoundary($boundary);
        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('MyRchBoundary', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('rch', strtolower($response->type));
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"Polygon","coordinates":[[[0,0],[0,1],[1,1],[1,0],[0,0]]]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);

        $model = $this->modelManager->findById($model->getId());
        $this->assertCount(1, $model->getBoundaries());
    }

    public function testGetWelBoundaryDetailsByBoundaryId()
    {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createWel()
            ->setGeometry(new Point(0,1))
            ->setName('MyWelBoundary');

        $model->addBoundary($boundary);
        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('MyWelBoundary', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('wel', strtolower($response->type));
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"Point","coordinates":[0,1]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);

        $model = $this->modelManager->findById($model->getId());
        $this->assertCount(1, $model->getBoundaries());
    }

    public function testGetRivBoundaryDetailsByBoundaryId()
    {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createRiv()
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyRivBoundary');

        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('MyRivBoundary', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('riv', strtolower($response->type));
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"LineString","coordinates":[[0,0],[1,1]]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);

        $model = $this->modelManager->findById($model->getId());
        $this->assertCount(1, $model->getBoundaries());
    }

    public function testPutChdBoundaryDetailsByBoundaryId(){
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyBoundary');

        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            ),
            array(
                'name' => 'NewBoundaryName',
                'layer_numbers' => array(1,2,4),
                'geometry' => '{"type":"LineString","coordinates":[[0,0],[2,2],[3,3]]}'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('NewBoundaryName', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('chd', strtolower($response->type));
        $this->assertObjectHasAttribute('layer_numbers', $response);
        $this->assertEquals(array(1,2,4), $response->layer_numbers);
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"LineString","coordinates":[[0,0],[2,2],[3,3]]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);
    }

    public function testPutGhbBoundaryDetailsByBoundaryId(){
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createGhb()
            ->setLayerNumbers(array(1,2,3))
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyBoundary');

        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            ),
            array(
                'name' => 'NewBoundaryName',
                'layer_numbers' => array(1,2,4),
                'geometry' => '{"type":"LineString","coordinates":[[0,0],[2,2],[3,3]]}'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('NewBoundaryName', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('ghb', strtolower($response->type));
        $this->assertObjectHasAttribute('layer_numbers', $response);
        $this->assertEquals(array(1,2,4), $response->layer_numbers);
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"LineString","coordinates":[[0,0],[2,2],[3,3]]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);
    }

    public function testPutRchBoundaryDetailsByBoundaryId(){
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createRch()
            ->setGeometry(new Polygon(array(
                [
                    new Point(0,0),
                    new Point(0,1),
                    new Point(1,1),
                    new Point(1,0),
                    new Point(0,0)
                ]
            )))
            ->setName('MyRchBoundary');

        $model->addBoundary($boundary);
        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            ),
            array(
                'name' => 'NewBoundaryName',
                'geometry' => '{"type":"Polygon","coordinates":[[[0,0],[0,1],[1,1],[1,2],[2,3],[1,0],[0,0]]]}'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('NewBoundaryName', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('rch', strtolower($response->type));
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"Polygon","coordinates":[[[0,0],[0,1],[1,1],[1,2],[2,3],[1,0],[0,0]]]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);
    }

    public function testPutRivBoundaryDetailsByBoundaryId(){
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createRiv()
            ->setGeometry(new LineString([[0,0],[1,1]]))
            ->setName('MyRivBoundary');

        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            ),
            array(
                'name' => 'NewBoundaryName',
                'geometry' => '{"type":"LineString","coordinates":[[0,0],[2,2],[3,3]]}'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('NewBoundaryName', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('riv', strtolower($response->type));
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"LineString","coordinates":[[0,0],[2,2],[3,3]]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);
    }

    public function testPutWelBoundaryDetailsByBoundaryId(){
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $boundary = BoundaryFactory::createWel()
            ->setGeometry(new Point(0,1))
            ->setWellType('nwt')
            ->setName('MyWelBoundary');

        $model->addBoundary($boundary);

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf(
                '/api/modflow/boundaries/%s.json',
                $boundary->getId()->toString()
            ),
            array(
                'name' => 'NewBoundaryName',
                'geometry' => '{"type":"Point","coordinates":[2,2]}',
                'well_type' => 'abt'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('NewBoundaryName', $response->name);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('type', $response);
        $this->assertEquals('wel', strtolower($response->type));
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"Point","coordinates":[2,2]}', $response->geometry);
        $this->assertObjectHasAttribute('observation_points', $response);
        $this->assertObjectHasAttribute('well_type', $response);
        $this->assertEquals('abt', $response->well_type);
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
