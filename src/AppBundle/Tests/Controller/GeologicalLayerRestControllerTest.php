<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\ProjectFactory;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeologicalLayerRestControllerTest extends WebTestCase
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
    protected $projectName = "TestLayersProject";

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

        $geologicalLayer = GeologicalLayerFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'L1', true);
        $this->entityManager->persist($geologicalLayer);
        $geologicalLayer = GeologicalLayerFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'L2', true);
        $this->entityManager->persist($geologicalLayer);
        $geologicalLayer = GeologicalLayerFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'L3', true);
        $this->entityManager->persist($geologicalLayer);
        $this->entityManager->flush();

    }

    /**
     * Test for the API-Call /api/users/<username>/projects.json
     * which is providing a list of projects of the user
     */
    public function testProjectLayersListController()
    {
        $client = static::createClient();
        $client->request('GET', '/api/projects/'.$this->project->getId().'/geologicallayers.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var ArrayCollection $geologicaLLayers */
        $geologicalLayers = $this->serializer->deserialize($client->getResponse()->getContent(), 'array<AppBundle\Entity\GeologicalLayer>', 'json');
        $this->assertCount(3, $geologicalLayers);

        dump($client->getResponse()->getContent());

        /** @var GeologicalLayer $geologicalLayer */
        foreach ($geologicalLayers as $geologicalLayer)
        {
            $id = $geologicalLayer->getId();
            $name = $geologicalLayer->getName();
            $this->assertTrue($geologicalLayer->getPublic());

            $entity = $this->entityManager->getRepository('AppBundle:GeologicalLayer')
                ->findOneBy(array(
                    'id' => $id,
                    'name' => $name
                ));
            $this->assertNotNull($entity);
        }
    }

    public function rojectDetailsApiCall()
    {
        $client = static::createClient();
        $client->request('GET', '/api/projects/'.$this->project->getId().'.json');
        $this->assertEquals(220, $client->getResponse()->getStatusCode());

        /** @var Project $project */
        $project = $this->serializer->deserialize($client->getResponse()->getContent(), 'AppBundle\Entity\Project', 'json');
        $this->assertEquals($this->project->getId(), $project->getId());
        $this->assertEquals($this->project->getName(), $project->getName());
        $this->assertEquals($this->project->getDescription(), $project->getDescription());
        $this->assertEquals($this->project->getOwner()->getId(), $project->getOwner()->getId());
        $this->assertEquals($this->project->getOwner()->getUsername(), $project->getOwner()->getUsername());
        $this->assertEquals($this->project->getOwner()->getEmail(), $project->getOwner()->getEmail());
        $this->assertTrue($this->project->getParticipants()->contains($this->participant));

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

        $geologicalLayers = $this->entityManager
            ->getRepository('AppBundle:GeologicalLayer')
            ->findAll();

        foreach ($geologicalLayers as $geologicalLayer)
        {
            $this->entityManager->remove($geologicalLayer);
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
