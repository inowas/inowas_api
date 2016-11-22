<?php

namespace Inowas\ModflowBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Service\ModflowModelManager;
use Inowas\SoilmodelBundle\Service\SoilmodelManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModelBoundaryControllerTest extends WebTestCase
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
            sprintf('/api/modflow/model/%s/boundary.json', $model->getId()->toString()),
            array(
                'name' => 'MyBoundary',
                'type' => 'chd'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $model = $this->modelManager->findById($model->getId());
        $this->assertCount(1, $model->getBoundaries());
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
