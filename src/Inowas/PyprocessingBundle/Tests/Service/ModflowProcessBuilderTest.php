<?php

namespace Inowas\PyprocessingBundle\Tests\Service;

use Inowas\PyprocessingBundle\Model\Modflow\ModflowResultRasterParameter;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowResultTimeSeriesParameter;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcess;
use Inowas\PyprocessingBundle\Service\ConfigurationFileCreatorFactory;
use Inowas\PyprocessingBundle\Service\ModflowProcessBuilder;
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
