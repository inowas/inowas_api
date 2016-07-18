<?php

namespace Inowas\PyprocessingBundle\Tests\Model\PythonProcess;

use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcess;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessFactory;

class PythonProcessFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PythonProcessConfiguration
     */
    protected $pythonProcess;

    public function setUp(){
        $pythonProcessConfiguration = new PythonProcessConfiguration();
        $this->pythonProcess = PythonProcessFactory::create($pythonProcessConfiguration);
    }

    public function testInstantiate(){
        $this->assertInstanceOf(PythonProcess::class, $this->pythonProcess);
    }
}
