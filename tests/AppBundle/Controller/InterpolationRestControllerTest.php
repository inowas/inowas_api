<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InterpolationRestControllerTest extends WebTestCase
{
    public function setUp()
    {
        self::bootKernel();
    }

    public function testPostResult()
    {
        $this->assertTrue(true);
    }
}
