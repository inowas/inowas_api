<?php

namespace Inowas\ModflowBundle\Tests\Controller;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Model\AreaFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Service\ModflowModelManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AreaControllerTest extends WebTestCase
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

    public function testGetAreaByModelId() {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');

        $model->setArea(AreaFactory::create()
            ->setName('MyArea')
            ->setGeometry(
                new Polygon(array(
                    array(
                        new Point(1,2),
                        new Point(2,2),
                        new Point(2,1),
                        new Point(1,1),
                        new Point(1,2)
                    )), 4326
                )
            )
        );

        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/modflow/model/%s/area.json', $model->getId()->toString())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertEquals($model->getArea()->getId()->toString(), $response->id);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"Polygon","coordinates":[[[1,2],[2,2],[2,1],[1,1],[1,2]]]}', $response->geometry);
    }

    public function testPutAreaByModelId() {
        $model = $this->modelManager->create();
        $model->setStart(new \DateTime('2016-01-01'));
        $model->setEnd(new \DateTime('2016-12-31'));
        $model->setName('TestModel');
        $this->modelManager->update($model);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf('/api/modflow/model/%s/area.json', $model->getId()->toString()),
            array(
                'name' => 'AreaName',
                'geometry' => '{"type":"Polygon","coordinates":[[[1,3],[2,2],[2,1],[1,1],[1,3]]]}'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertEquals($model->getArea()->getId()->toString(), $response->id);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertObjectHasAttribute('geometry', $response);
        $this->assertEquals('{"type":"Polygon","coordinates":[[[1,3],[2,2],[2,1],[1,1],[1,3]]]}', $response->geometry);
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
