<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\BoundaryModelObject;
use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Entity\User;
use AppBundle\Entity\Well;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\UserFactory;
use AppBundle\Model\WellFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoundaryRestControllerTest extends WebTestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var  GeneralHeadBoundary $ghb */
    protected $ghb;

    /** @var  ConstantHeadBoundary $chb */
    protected $chb;

    /** @var  StreamBoundary $riv */
    protected $riv;

    /** @var  Well $wel */
    protected $wel;

    /** @var  PropertyType $headPropertyType */
    protected $headPropertyType;

    /** @var  PropertyType $riverStagePropertyType */
    protected $riverStagePropertyType;

    /** @var  PropertyType $riverStageConductancePropertyType */
    protected $riverStageConductancePropertyType;

    /** @var  PropertyType $pumpingRatePropertyType */
    protected $pumpingRatePropertyType;

    /** @var  GeologicalLayer $layer */
    protected $layer;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->headPropertyType = new PropertyType();
        $this->headPropertyType->setName('Head')
            ->setAbbreviation('hd')
        ;

        $this->riverStagePropertyType = new PropertyType();
        $this->riverStagePropertyType->setName('RiverStage')
            ->setAbbreviation('rs')
        ;

        $this->riverStageConductancePropertyType = new PropertyType();
        $this->riverStageConductancePropertyType->setName('RiverStageConductance')
            ->setAbbreviation('rsc')
        ;

        $this->pumpingRatePropertyType = new PropertyType();
        $this->pumpingRatePropertyType->setName('Pumpingrate')
            ->setAbbreviation('pr')
        ;

        $this->owner = UserFactory::createTestUser('BoundaryOwner');
        $this->entityManager->persist($this->owner);
        $this->entityManager->flush();

        $this->layer = GeologicalLayerFactory::create()
            ->setName('New Layer')
            ->setOrder(GeologicalLayer::TOP_LAYER)
        ;
        $this->entityManager->persist($this->layer);

        $this->chb = ConstantHeadBoundaryFactory::create()
            ->setName('GHB-Boundary')
            ->setPublic(true)
            ->setOwner($this->owner)
            ->setGeometry(new LineString(
                    array(
                        new Point(11777056.49104572273790836, 2403440.17028302047401667),
                        new Point(11777973.9436037577688694, 2403506.49811625294387341),
                        new Point(11780228.12698311358690262, 2402856.2682070448063314),
                        new Point(11781703.59880801662802696, 2401713.22520185634493828)
                    ))
            )
            ->addValue($this->headPropertyType, PropertyValueFactory::create()->setValue(10))
        ;

        $this->entityManager->persist($this->chb);

        $this->ghb = GeneralHeadBoundaryFactory::create()
            ->setName('GHB-Boundary')
            ->setPublic(true)
            ->setOwner($this->owner)
            ->setGeometry(new LineString(
                array(
                    new Point(11777056.49104572273790836, 2403440.17028302047401667),
                    new Point(11777973.9436037577688694, 2403506.49811625294387341),
                    new Point(11780228.12698311358690262, 2402856.2682070448063314),
                    new Point(11781703.59880801662802696, 2401713.22520185634493828)
                ))
            )
            ->addValue($this->headPropertyType, PropertyValueFactory::create()->setValue(10));
        ;

        $this->entityManager->persist($this->ghb);

        $this->riv = StreamBoundaryFactory::create()
            ->setName('RIV-Boundary')
            ->setPublic(true)
            ->setOwner($this->owner)
            ->setStartingPoint(new Point(10, 11))
            ->setGeometry(new LineString(
                array(
                    new Point(10, 11),
                    new Point(15, 21),
                    new Point(23, 99)
                )))
            ->addValue($this->riverStagePropertyType, PropertyValueFactory::create()->setValue(12))
            ->addValue($this->riverStageConductancePropertyType, PropertyValueFactory::create()->setValue(0.001))
        ;

        $this->entityManager->persist($this->riv);

        $this->wel = WellFactory::create()
            ->setName('WEL-Boundary')
            ->setPublic(true)
            ->setOwner($this->owner)
            ->setPoint(new Point(10, 11))
            ->setLayer($this->layer)
            ->addValue($this->pumpingRatePropertyType, PropertyValueFactory::create()->setValue(-1000))
        ;

        $this->entityManager->persist($this->wel);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/boundaries/<id>.json
     * which is providing a the details of the specific boundary
     */
    public function testConstantHeadsBoundaryDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/boundaries/'.$this->chb->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $boundary = json_decode($client->getResponse()->getContent());
        $this->assertEquals($this->chb->getId(), $boundary->id);
        $this->assertEquals($this->chb->getName(), $boundary->name);
        $this->assertEquals('CHB', $boundary->type);
        $this->assertObjectHasAttribute('geometry', $boundary);
        $this->assertObjectHasAttribute('properties', $boundary);
        $this->assertCount(1, $boundary->properties);
        $property = $boundary->properties[0];
        $this->assertObjectHasAttribute('id', $property);
        $this->assertObjectHasAttribute('property_type', $property);
        $this->assertObjectHasAttribute('values', $property);
        $this->assertCount(1, $property->values);
        $value = $property->values[0];
        $this->assertEquals(10, $value->value);
    }

    /**
     * Test for the API-Call /api/boundaries/<id>.json
     * which is providing a the details of the specific boundary
     */
    public function testGeneralHeadsBoundaryDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/boundaries/'.$this->ghb->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $boundary = json_decode($client->getResponse()->getContent());
        $this->assertEquals($this->ghb->getId(), $boundary->id);
        $this->assertEquals($this->ghb->getName(), $boundary->name);
        $this->assertEquals('GHB', $boundary->type);
        $this->assertObjectHasAttribute('geometry', $boundary);
        $this->assertObjectHasAttribute('properties', $boundary);
        $this->assertCount(1, $boundary->properties);
        $property = $boundary->properties[0];
        $this->assertObjectHasAttribute('id', $property);
        $this->assertObjectHasAttribute('property_type', $property);
        $this->assertObjectHasAttribute('values', $property);
        $this->assertCount(1, $property->values);
        $value = $property->values[0];
        $this->assertEquals(10, $value->value);
    }

    /**
     * Test for the API-Call /api/boundaries/<id>.json
     * which is providing a the details of the specific boundary
     */
    public function testRiverBoundaryDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/boundaries/' . $this->riv->getId() . '.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $boundary = json_decode($client->getResponse()->getContent());
        $this->assertEquals($this->riv->getId(), $boundary->id);
        $this->assertEquals($this->riv->getName(), $boundary->name);
        $this->assertEquals('RIV', $boundary->type);
        $this->assertObjectHasAttribute('starting_point', $boundary);
        $this->assertObjectHasAttribute('line', $boundary);
        $this->assertObjectHasAttribute('properties', $boundary);
        $this->assertCount(2, $boundary->properties);
        $property = $boundary->properties[0];
        $this->assertObjectHasAttribute('id', $property);
        $this->assertObjectHasAttribute('property_type', $property);
        $this->assertObjectHasAttribute('values', $property);
        $this->assertCount(1, $property->values);
        $property = $boundary->properties[1];
        $this->assertObjectHasAttribute('id', $property);
        $this->assertObjectHasAttribute('property_type', $property);
        $this->assertObjectHasAttribute('values', $property);
        $this->assertCount(1, $property->values);
    }

    /**
     * Test for the API-Call /api/boundaries/<id>.json
     * which is providing a the details of the specific boundary
     */
    public function testWellBoundaryDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/boundaries/' . $this->wel->getId() . '.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $boundary = json_decode($client->getResponse()->getContent());
        $this->assertEquals($this->wel->getId(), $boundary->id);
        $this->assertEquals($this->wel->getName(), $boundary->name);
        $this->assertEquals('WEL', $boundary->type);
        $this->assertObjectHasAttribute('point', $boundary);
        $this->assertObjectHasAttribute('properties', $boundary);
        $this->assertCount(1, $boundary->properties);
        $property = $boundary->properties[0];
        $this->assertObjectHasAttribute('id', $property);
        $this->assertObjectHasAttribute('property_type', $property);
        $this->assertObjectHasAttribute('values', $property);
        $this->assertCount(1, $property->values);
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

        $entities = $this->entityManager->getRepository('AppBundle:ModelObject')
            ->findBy(array(
                'owner' => $user
            ));

        foreach ($entities as $entity) {
            if ($entity instanceof BoundaryModelObject) {
                $this->entityManager->remove($entity);
            }
        }

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
