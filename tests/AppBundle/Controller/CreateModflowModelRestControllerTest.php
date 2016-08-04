<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\RestControllerTestCase;

class CreateModflowModelRestControllerTest extends RestControllerTestCase
{
    public function setUp()
    {
        $this->getEntityManager()->persist($this->getUser());
        $this->getEntityManager()->flush();
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
            array('json' => '{"area":{"geoJSON":"{\"type\":\"Feature\",\"properties\":{},\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[12.564239501953123,41.72213058512578],[13.119049072265625,41.90840946591109],[13.594207763671875,41.51269075845857],[13.168487548828123,41.475660200278234],[13.105316162109375,41.759019938155404],[12.808685302734375,41.60722821271717],[12.564239501953123,41.72213058512578]]]}}"},"grid_size":{"cols":"10","rows":"10"},"soil_model":{"numberOfLayers":"1"},"name":"myModelName","description":"myModelDescription"}'),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getUser()->getApiKey())
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
        $user = $this->getEntityManager()->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->getUser()->getUsername()
            ));
        $this->getEntityManager()->remove($user);

        $model = $this->getEntityManager()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'name' => "myModelName"
            ));

        $this->getEntityManager()->remove($model);
        $this->getEntityManager()->flush();
    }
}
