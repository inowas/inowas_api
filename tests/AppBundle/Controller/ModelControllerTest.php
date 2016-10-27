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
