<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Model\ProjectFactory;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var Serializer */
    protected $serializer;

    /** @var User $owner */
    protected $owner;

    /** @var  User $participant */
    protected $participant;

    /** @var Project $project */
    protected $project;

    protected $ownerUserName = "ownerUserName";
    protected $participantUserName = "participantUserName";
    protected $projectName = "TestProject";

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer')
        ;

        $this->owner = new User();
        $this->owner->setUsername($this->ownerUserName);
        $this->owner->setEmail($this->ownerUserName.'@email.com');
        $this->owner->setPassword('password');
        $this->owner->setEnabled(true);
        $this->entityManager->persist($this->owner);

        $this->participant = new User();
        $this->participant->setUsername($this->participantUserName);
        $this->participant->setEmail($this->participantUserName.'@email.com');
        $this->participant->setPassword('password');
        $this->participant->setEnabled(true);
        $this->entityManager->persist($this->participant);
        $this->entityManager->flush();

        $this->project = ProjectFactory::setOwnerAndPublic($this->owner, true);
        $this->project->setName($this->projectName);
        $this->project->setDescription('TestProjectDescription!!!');
        $this->project->addParticipant($this->participant);
        $this->entityManager->persist($this->project);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/projects.json
     * which is providing a list of projects of the user
     */
    public function testUsersProjectsListController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/projects.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Project[] $projectArray */
        $projectArray = $this->serializer->deserialize($client->getResponse()->getContent(), 'array<AppBundle\Entity\Project>', 'json');
        $this->assertCount(1, $projectArray);
        $project = $projectArray[0];
        $this->assertEquals($this->project->getId(), $project->getId());
        $this->assertEquals($this->project->getName(), $project->getName());
        $this->assertEquals($this->project->getDescription(), $project->getDescription());
        $this->assertEquals($this->project->getPublic(), $project->getPublic());
        $this->assertEquals($this->project->getDateCreated(), $project->getDateCreated());
        $this->assertEquals($this->project->getDateModified(), $project->getDateModified());
    }

    public function testProjectDetailsApiCall()
    {
        $client = static::createClient();
        $client->request('GET', '/api/projects/'.$this->project->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Project $project */
        $project = $this->serializer->deserialize($client->getResponse()->getContent(), 'AppBundle\Entity\Project', 'json');
        $this->assertEquals($this->project->getId(), $project->getId());
        $this->assertEquals($this->project->getName(), $project->getName());
        $this->assertEquals($this->project->getDescription(), $project->getDescription());
        $this->assertEquals($this->project->getPublic(), $project->getPublic());
        $this->assertEquals($this->project->getDateCreated(), $project->getDateCreated());
        $this->assertEquals($this->project->getDateModified(), $project->getDateModified());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->owner->getUsername()
            ));
        $this->entityManager->remove($user);

        $participant = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->participant->getUsername()
            ));
        $this->entityManager->remove($participant);

        $project = $this->entityManager->getRepository('AppBundle:Project')
            ->findOneBy(array(
               'name' => $this->projectName
            ));
        $this->entityManager->remove($project);


        $this->entityManager->flush();

        $this->entityManager->close();
    }
}
