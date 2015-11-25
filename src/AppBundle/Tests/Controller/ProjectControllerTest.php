<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Model\ProjectFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var Project $project
     */
    protected $project;


    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $this->user = new User();
        $this->user->setUsername('userProjectControllerTest');
        $this->user->setEmail('userProjectControllerTest@email.com');
        $this->user->setPassword('userProjectControllerTestPassword');
        $this->user->setEnabled(true);
        $this->entityManager->persist($this->user);

        $this->project = ProjectFactory::setOwnerAndPublic($this->user, true);
        $this->project->setName('TestProject');
        $this->project->setDescription('This is the description of the TestProject.');
        $this->entityManager->persist($this->project);

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     *
     */
    public function testUsersProjectsListController()
    {
        $users = $this->entityManager->getRepository('AppBundle:User')
            ->findAll();
        $this->assertCount(1, $users);

        $client = static::createClient();
        $crawler = $client->request('GET', '/api/users/'.$this->user->getUsername().'/projects.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->entityManager->remove($this->user);
        $this->entityManager->remove($this->project);
        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
