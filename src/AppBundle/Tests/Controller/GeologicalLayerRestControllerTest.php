<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\Point;
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

        $geologicalLayer1 = GeologicalLayerFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'L1', true);
        $this->entityManager->persist($geologicalLayer1);
        $geologicalLayer2 = GeologicalLayerFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'L2', true);
        $this->entityManager->persist($geologicalLayer2);
        $geologicalLayer3 = GeologicalLayerFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'L3', true);
        $this->entityManager->persist($geologicalLayer3);
        $this->entityManager->flush();

        $geologicalPoint = GeologicalPointFactory::setOwnerProjectNameAndPoint($this->owner, $this->project, 'GP1', new Point(1,2,3), true);
        $this->entityManager->persist($geologicalPoint);

        $geologicalUnit = GeologicalUnitFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'GP1.1', true);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(100);
        $geologicalUnit->setBottomElevation(90);
        $geologicalUnit->addGeologicalLayer($geologicalLayer1);
        $this->entityManager->persist($geologicalUnit);

        $geologicalUnit = GeologicalUnitFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'GP1.2', true);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(90);
        $geologicalUnit->setBottomElevation(80);
        $geologicalUnit->addGeologicalLayer($geologicalLayer2);
        $this->entityManager->persist($geologicalUnit);

        $geologicalUnit = GeologicalUnitFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'GP1.3', true);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(80);
        $geologicalUnit->setBottomElevation(70);
        $geologicalUnit->addGeologicalLayer($geologicalLayer3);
        $this->entityManager->persist($geologicalUnit);

        $geologicalPoint = GeologicalPointFactory::setOwnerProjectNameAndPoint($this->owner, $this->project, 'GP2', new Point(10, 20, 30), true);
        $this->entityManager->persist($geologicalPoint);

        $geologicalUnit = GeologicalUnitFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'GP2.1', true);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(100);
        $geologicalUnit->setBottomElevation(90);
        $geologicalUnit->addGeologicalLayer($geologicalLayer1);
        $this->entityManager->persist($geologicalUnit);

        $geologicalUnit = GeologicalUnitFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'GP2.2', true);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(90);
        $geologicalUnit->setBottomElevation(80);
        $geologicalUnit->addGeologicalLayer($geologicalLayer2);
        $this->entityManager->persist($geologicalUnit);

        $geologicalUnit = GeologicalUnitFactory::setOwnerProjectNameAndPublic($this->owner, $this->project, 'GP2.3', true);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(80);
        $geologicalUnit->setBottomElevation(70);
        $geologicalUnit->addGeologicalLayer($geologicalLayer3);
        $this->entityManager->persist($geologicalUnit);

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

    public function testProjectLayerDetailsController()
    {
        $geologicalLayersInDB = $this->entityManager->getRepository('AppBundle:GeologicalLayer')
            ->findAll();

        foreach ($geologicalLayersInDB as $geologicalLayerInDB)
        {
            $client = static::createClient();
            $client->request('GET', '/api/projects/'.$this->project->getId().'/geologicallayers/'.$geologicalLayerInDB->getId().'.json');
            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            /** @var GeologicalLayer $geologicalLayer */
            $geologicalLayer = $this->serializer->deserialize($client->getResponse()->getContent(), 'AppBundle\Entity\GeologicalLayer', 'json');

            $this->assertEquals($geologicalLayerInDB->getId(), $geologicalLayer->getId());
            $this->assertEquals($geologicalLayerInDB->getName(), $geologicalLayer->getName());
            $this->assertEquals($geologicalLayerInDB->getPublic(), $geologicalLayer->getPublic());

            // TODO
            // Add more tests for the geological units

            $this->assertEquals(2, count($geologicalLayer->getGeologicalUnits()));
            $geologicalUnits = $geologicalLayerInDB->getGeologicalUnits();
            foreach ($geologicalUnits as $geologicalUnit)
            {
            }
        }
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

        $geologicalPoints = $this->entityManager
            ->getRepository('AppBundle:GeologicalPoint')
            ->findAll();

        foreach ($geologicalPoints as $geologicalPoint)
        {
            $this->entityManager->remove($geologicalPoint);
        }


        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
