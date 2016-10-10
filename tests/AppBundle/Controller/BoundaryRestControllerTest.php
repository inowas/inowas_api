<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\BoundaryModelObject;
use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Entity\User;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\WellBoundaryFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Tests\AppBundle\RestControllerTestCase;

class BoundaryRestControllerTest extends RestControllerTestCase
{
    /** @var  GeneralHeadBoundary $ghb */
    protected $ghb;

    /** @var  ConstantHeadBoundary $chb */
    protected $chb;

    /** @var  StreamBoundary $riv */
    protected $riv;

    /** @var  WellBoundary $wel */
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
        $this->headPropertyType = PropertyTypeFactory::create(PropertyType::HYDRAULIC_HEAD);
        $this->riverStagePropertyType = PropertyTypeFactory::create(PropertyType::RIVER_STAGE);
        $this->riverStageConductancePropertyType = PropertyTypeFactory::create(PropertyType::RIVERBED_CONDUCTANCE);
        $this->pumpingRatePropertyType = PropertyTypeFactory::create(PropertyType::PUMPING_RATE);

        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->flush();

        $this->layer = GeologicalLayerFactory::create()
            ->setName('New Layer')
            ->setOrder(GeologicalLayer::TOP_LAYER)
        ;
        $this->getEntityManager()->persist($this->layer);

        $this->chb = ConstantHeadBoundaryFactory::create()
            ->setName('GHB-Boundary')
            ->setPublic(true)
            ->setOwner($this->getOwner())
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

        $this->getEntityManager()->persist($this->chb);

        $this->ghb = GeneralHeadBoundaryFactory::create()
            ->setName('GHB-Boundary')
            ->setPublic(true)
            ->setOwner($this->getOwner())
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

        $this->getEntityManager()->persist($this->ghb);

        $this->riv = StreamBoundaryFactory::create()
            ->setName('RIV-Boundary')
            ->setPublic(true)
            ->setOwner($this->getOwner())
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

        $this->getEntityManager()->persist($this->riv);

        $this->wel = WellBoundaryFactory::create()
            ->setName('WEL-Boundary')
            ->setPublic(true)
            ->setOwner($this->getOwner())
            ->setGeometry(new Point(10, 11))
            ->setLayer($this->layer)
            ->addValue($this->pumpingRatePropertyType, PropertyValueFactory::create()->setValue(-1000))
        ;

        $this->getEntityManager()->persist($this->wel);
        $this->getEntityManager()->flush();
    }

    public function testBoundaryWithoutAPIKeyThrows401()
    {
        $client = static::createClient();
        $client->request('GET', '/api/boundaries/4d3a6a77-2746-4ea0-884d-79fafcb34a81.json');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/boundaries/<id>.json
     * which is providing a the details of the specific boundary
     */
    public function testConstantHeadsBoundaryDetails()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/boundaries/'.$this->chb->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

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
        $client->request(
            'GET', '/api/boundaries/'.$this->ghb->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

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
        $client->request('GET', '/api/boundaries/' . $this->riv->getId() . '.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

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
        $client->request('GET', '/api/boundaries/' . $this->wel->getId() . '.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

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

    public function testBoundaryWithInvalidIdThrows404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/boundaries/invalid_id.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testBoundaryWithUnknownIdThrows404()
    {
        $client = static::createClient();
        $client->request('GET',
            '/api/boundaries/4d3a6a77-2746-4ea0-884d-79fafcb34a81.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->getEntityManager()->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->getOwner()->getUsername()
            ));
        $this->getEntityManager()->remove($user);

        $entities = $this->getEntityManager()->getRepository('AppBundle:ModelObject')
            ->findBy(array(
                'owner' => $user
            ));

        foreach ($entities as $entity) {
            if ($entity instanceof BoundaryModelObject) {
                $this->getEntityManager()->remove($entity);
            }
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}
