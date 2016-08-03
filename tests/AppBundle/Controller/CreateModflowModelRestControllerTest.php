<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateModflowModelRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');
    }

    /**
     * Test for the API-POST-Call /api/modflowmodels.json
     * @todo make some more asserts
     */
    public function testPostCreateModflowModel()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/modflowmodels.json',
            array('json' => '{"area":{"geoJSON":"{\"type\":\"Feature\",\"properties\":{},\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[12.564239501953123,41.72213058512578],[13.119049072265625,41.90840946591109],[13.594207763671875,41.51269075845857],[13.168487548828123,41.475660200278234],[13.105316162109375,41.759019938155404],[12.808685302734375,41.60722821271717],[12.564239501953123,41.72213058512578]]]}}"},"grid_size":{"cols":"10","rows":"10"},"soil_model":{"numberOfLayers":"1"},"name":"myModelName","description":"myModelDescription"}')
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $model = json_decode($response);
        $this->assertObjectHasAttribute('id', $model);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $model = $this->entityManager->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'name' => "myModelName"
            ));

        $this->entityManager->remove($model);
        $this->entityManager->flush();
    }
}
