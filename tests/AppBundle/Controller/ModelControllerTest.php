<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ModFlowModelFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModelControllerTest extends WebTestCase
{

    /** @var  EntityManagerInterface */
    protected $em;

    /** @var  User $user */
    protected $user;

    /** @var ArrayCollection */
    protected $models;

    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');

        $userManager = static::$kernel->getContainer()->get('fos_user.user_manager');
        $this->user = $userManager->createUser();
        $this->user->setUsername('TestUser'.rand(1000000,20000000));
        $this->user->setEmail('TestUser'.rand(1000000,20000000));
        $this->user->setPlainPassword('TestPassword');
        $this->user->setEnabled(true);
        $userManager->updateUser($this->user);

        $this->models = new ArrayCollection();
        $this->models->add(ModFlowModelFactory::create()
            ->setName('Model_1')
            ->setDescription('ModelDescription_1')
            ->setOwner($this->user)
        );

        $this->models->add(ModFlowModelFactory::create()
            ->setName('Model_2')
            ->setDescription('ModelDescription_2')
            ->setOwner($this->user)
        );

        foreach ($this->models as $model){
            $this->em->persist($model);
        }

        $this->em->flush();
    }

    /**
     * Test if Login-Page loads
     */
    public function testLoginPageIfNotSignedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('title:contains("Login")')->count() > 0);
    }

    public function testModelsModflowList()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->followRedirects();
        $client->request('GET', '/models/modflow');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        foreach ($this->models as $model){
            $this->assertContains($model->getId()->toString(), $client->getResponse()->getContent());
        }
    }

    public function testModelModflowById()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.$this->models->first()->getId()->toString());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRedirectModelModflowWithInvalidId()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/123');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRedirectModelModflowWithUnknownId()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.Uuid::uuid4()->toString());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testModelModflowScenariosById()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.$this->models->first()->getId()->toString().'/scenarios');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRedirectModelModflowScenariosWithInvalidId()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/123/scenarios');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRedirectModelModflowScenariosWithUnknownId()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.Uuid::uuid4()->toString().'/scenarios');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testModelModflowScenarioResultsByModelId()
    {
        $model = $this->models->first();
        $scenario = ModelScenarioFactory::create($model)
            ->setName('TestScenario')
            ->setOwner($this->user)
            ->setPublic(true)
        ;

        $this->em->persist($scenario);
        $this->em->flush();

        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request(
            'GET',
            '/models/modflow/'.$model->getId()->toString().'/scenarios/results'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRedirectModelModflowScenarioResultsWithoutScenarios()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.$this->models->first()->getId()->toString().'/scenarios/results');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRedirectModelModflowScenariosResultsWithInvalidId()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/123/scenarios/results');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRedirectModelModflowScenariosResultsWithUnknownId()
    {
        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.Uuid::uuid4()->toString().'/scenarios/results');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testModelModflowScenarioByModelIdAndScenarioId()
    {
        $model = $this->models->first();
        $scenario = ModelScenarioFactory::create($model)
            ->setName('TestScenario')
            ->setOwner($this->user)
            ->setPublic(true);
        $this->em->persist($scenario);
        $this->em->flush();

        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.$model->getId()->toString().'/scenarios/'.$scenario->getId()->toString());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testModelModflowScenarioWithInvalidModelId()
    {
        $model = $this->models->first();
        $scenario = ModelScenarioFactory::create($model)
            ->setName('TestScenario')
            ->setOwner($this->user)
            ->setPublic(true);
        $this->em->persist($scenario);
        $this->em->flush();

        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/123/scenarios/'.$scenario->getId()->toString());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testModelModflowScenarioWithUnknownModelId()
    {
        $model = $this->models->first();
        $scenario = ModelScenarioFactory::create($model)
            ->setName('TestScenario')
            ->setOwner($this->user)
            ->setPublic(true);
        $this->em->persist($scenario);
        $this->em->flush();

        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.Uuid::uuid4()->toString().'/scenarios/'.$scenario->getId()->toString());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testModelModflowScenarioWithInvalidScenarioId()
    {
        $model = $this->models->first();
        $scenario = ModelScenarioFactory::create($model)
            ->setName('TestScenario')
            ->setOwner($this->user)
            ->setPublic(true);
        $this->em->persist($scenario);
        $this->em->flush();

        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.$model->getId()->toString().'/scenarios/123');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testModelModflowScenarioWithUnknowScenarioId()
    {
        $model = $this->models->first();
        $scenario = ModelScenarioFactory::create($model)
            ->setName('TestScenario')
            ->setOwner($this->user)
            ->setPublic(true);
        $this->em->persist($scenario);
        $this->em->flush();

        $client = $this->login($this->user->getUsername(), 'TestPassword');
        $client->request('GET', '/models/modflow/'.$model->getId()->toString().'/scenarios/'.Uuid::uuid4()->toString());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function login($username, $password)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[class=form-signin]')->form();
        $form->setValues(array(
            "_username" => $username,
            "_password" => $password,
        ));

        $client->submit($form);
        return $client;
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
