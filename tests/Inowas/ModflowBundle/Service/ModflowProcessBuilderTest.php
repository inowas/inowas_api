<?php

namespace Tests\Inowas\ModflowBundle\Service;

use AppBundle\Process\Modflow\ModflowResultRasterParameter;
use AppBundle\Process\Modflow\ModflowResultTimeSeriesParameter;
use AppBundle\Service\ConfigurationFileCreatorFactory;
use Inowas\ModflowBundle\Service\ModflowProcessBuilder;
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
        $this->assertInstanceOf('Inowas\ModflowBundle\Service\ModflowProcessBuilder', $this->modflowProcessBuilder);
    }

    public function testBuildCalculationProcess(){
        $calculationProcess = $this->modflowProcessBuilder->buildCalculationProcess('123');
        $this->assertInstanceOf('AppBundle\Process\PythonProcess', $calculationProcess);
    }

    public function testBuildRasterResultProcess(){
        $calculationProcess = $this->modflowProcessBuilder->buildRasterResultProcess('123', 1, array(1), array(2), ModflowResultRasterParameter::OP_RAW);
        $this->assertInstanceOf('AppBundle\Process\PythonProcess', $calculationProcess);
    }

    public function testBuildTimeseriesResultProcess(){
        $calculationProcess = $this->modflowProcessBuilder->buildTimeseriesResultProcess('123', 1, array(1), array(2), ModflowResultTimeSeriesParameter::OP_RAW);
        $this->assertInstanceOf('AppBundle\Process\PythonProcess', $calculationProcess);
    }
}
