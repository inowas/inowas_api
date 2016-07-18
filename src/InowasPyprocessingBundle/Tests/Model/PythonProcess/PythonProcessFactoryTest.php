<?php

namespace InowasPyprocessingBundle\Tests\Model\PythonProcess;

use InowasPyprocessingBundle\Model\PythonProcess\PythonProcess;
use InowasPyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;
use InowasPyprocessingBundle\Model\PythonProcess\PythonProcessFactory;

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
