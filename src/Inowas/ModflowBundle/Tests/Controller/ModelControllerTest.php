<?php

namespace Inowas\ModflowBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Service\ModflowModelManager;
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

    public function testGetModelById() {
        $model = $this->modelManager->create();
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
