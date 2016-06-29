<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ModFlowModelFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModelControllerTest extends WebTestCase
{

    /** @var  EntityManagerInterface */
    protected $em;

    /** @var ArrayCollection */
    protected $models;

    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');

        $this->models = new ArrayCollection();
        $this->models->add(ModFlowModelFactory::create()
            ->setName('Model_1')
            ->setDescription('ModelDescription_1')
        );

        $this->models->add(ModFlowModelFactory::create()
            ->setName('Model_2')
            ->setDescription('ModelDescription_2')
        );

        foreach ($this->models as $model){
            $this->em->persist($model);
        }

        $this->em->flush();
    }

    public function testModelsModflowList()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/models/modflow');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        foreach ($this->models as $model){
            $this->assertContains($model->getId()->toString(), $client->getResponse()->getContent());
        }

        unset($crawler);
    }

    public function testModelModflowById()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/models/modflow/'.$this->models->first()->getId()->toString());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        unset($crawler);
    }

    public function testModelModflowScenariosById()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/models/modflow/'.$this->models->first()->getId()->toString().'/scenarios');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        unset($crawler);
    }

    public function testRedirectModelModflowScenarioResultsWithoutScenarios()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/models/modflow/'.$this->models->first()->getId()->toString().'/scenarios/results');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        unset($crawler);
    }

    public function testModelModflowScenarioResultsByModelId()
    {
        $model = $this->models->first();
        $scenario = ModelScenarioFactory::create($model)
            ->setName('TestScenario');
        $this->em->persist($scenario);
        $this->em->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/models/modflow/'.$model->getId()->toString().'/scenarios/results');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        unset($crawler);
    }

    public function tearDown()
    {
        foreach ($this->models as $model){
            $model = $this->em->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array('id' => $model->getId()));
            $this->em->remove($model);
        }

        $this->em->flush();

    }
}
