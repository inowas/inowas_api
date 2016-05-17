<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InterpolationRestControllerTest extends WebTestCase
{


    public function setUp()
    {
        self::bootKernel();

    }

    public function testPostResult()
    {

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/interpolations.json'
        );
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
