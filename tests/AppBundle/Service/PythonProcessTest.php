<?php

namespace AppBundle\Tests\Service;

use AppBundle\Service\PythonProcess;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PythonProcessTest extends WebTestCase
{
    /** @var PythonProcess $pythonProcess */
    protected $pythonProcess;

    public function setUp()
    {
        self::bootKernel();

        $this->pythonProcess = static::$kernel->getContainer()
            ->get('inowas.python_process')
        ;
    }

    public function testPythonProcessReturnsProcess(){
        $this->assertInstanceOf('AppBundle\Service\PythonProcess', $this->pythonProcess);
    }

    public function testPythonProcessPreConfiguredPrefixIsSet(){
        $client = static::createClient();
        $this->assertEquals($client->getKernel()->getContainer()->getParameter('inowas.python_process.prefix'), $this->pythonProcess->getPrefix());
    }


}