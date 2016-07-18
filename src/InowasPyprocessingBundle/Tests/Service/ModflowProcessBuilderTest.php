<?php

namespace InowasPyprocessingBundle\Tests\Service;

use InowasPyprocessingBundle\Model\Modflow\ModflowResultRasterParameter;
use InowasPyprocessingBundle\Model\Modflow\ModflowResultTimeSeriesParameter;
use InowasPyprocessingBundle\Model\PythonProcess\PythonProcess;
use InowasPyprocessingBundle\Service\ConfigurationFileCreatorFactory;
use InowasPyprocessingBundle\Service\ModflowProcessBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModflowProcessBuilderTest extends KernelTestCase
{
    /** @var  ModflowProcessBuilder */
    protected $modflowProcessBuilder;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $serializer = $kernel->getContainer()->get('serializer');

        $configurationFileCreator = new ConfigurationFileCreatorFactory($kernel, $serializer);
        $this->modflowProcessBuilder = new ModflowProcessBuilder($kernel, $configurationFileCreator);
    }

    public function testInstantiate(){
        $this->assertInstanceOf(ModflowProcessBuilder::class, $this->modflowProcessBuilder);
    }

    public function testBuildCalculationProcess(){
        $calculationProcess = $this->modflowProcessBuilder->buildCalculationProcess('123');
        $this->assertInstanceOf(PythonProcess::class, $calculationProcess);
    }

    public function testBuildRasterResultProcess(){
        $calculationProcess = $this->modflowProcessBuilder->buildRasterResultProcess('123', 1, array(1), array(2), ModflowResultRasterParameter::OP_RAW);
        $this->assertInstanceOf(PythonProcess::class, $calculationProcess);
    }

    public function testBuildTimeseriesResultProcess(){
        $calculationProcess = $this->modflowProcessBuilder->buildTimeseriesResultProcess('123', 1, array(1), array(2), ModflowResultTimeSeriesParameter::OP_RAW);
        $this->assertInstanceOf(PythonProcess::class, $calculationProcess);
    }
}
