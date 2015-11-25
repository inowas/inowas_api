<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Model\ProjectFactory;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var Serializer */
    protected $serializer;

    /** @var User $user */
    protected $user;

    /** @var string  */
    protected $username = 'userprojectcontrollertest';

    /** @var Project $project */
    protected $project;

    /** @var string  */
    protected $projectname = 'TestProject';

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer');

        // Setup
        $this->user = new User();
        $this->user->setUsername($this->username);
        $this->user->setEmail('userprojectcontrollertestemail');
        $this->user->setPassword('userprojectcontrollertestPassword');
        $this->user->setEnabled(true);
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $this->project = ProjectFactory::setOwnerAndPublic($this->user, true);
        $this->project->setName($this->projectname);
        $this->project->setDescription('TestProjectDescription!!!');
        $this->entityManager->persist($this->project);
        $this->entityManager->flush();
    }

    /**
     *
     */
    public function testUsersProjectsListController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->user->getUsername().'/projects.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Project[] $projectArray */
        $projectArray = $this->serializer->deserialize($client->getResponse()->getContent(), 'array<AppBundle\Entity\Project>', 'json');
        $this->assertCount(1, $projectArray);
        $project = $projectArray[0];
        $this->assertEquals($this->projectname, $project->getName());
        $this->assertEquals($this->project->getId(), $project->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->username
            ));

        $project = $this->entityManager->getRepository('AppBundle:Project')
            ->findOneBy(array(
               'name' => $this->projectname
            ));

        $this->entityManager->remove($user);
        $this->entityManager->remove($project);
        $this->entityManager->flush();

        $this->entityManager->close();
    }
}
