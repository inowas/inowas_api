<?php

namespace InowasPyprocessingBundle\Tests\Model\GeoImage;

use InowasPyprocessingBundle\Model\GeoImage\GeoImageProcessConfiguration;
use InowasPyprocessingBundle\Model\PythonProcess\ProcessFile;

class GeoImageProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  GeoImageProcessConfiguration */
    protected $geoImageProcessConfiguration;

    public function setUp(){

        $inputFile = $this->getMockBuilder(ProcessFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $inputFile->method('getFileName')->willReturn('inputFilename');

        $outputFile = $this->getMockBuilder(ProcessFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $outputFile->method('getFileName')->willReturn('outputFilename');

        $this->geoImageProcessConfiguration = new GeoImageProcessConfiguration($inputFile, $outputFile);
    }

    public function testInstantiate(){
        $this->assertInstanceOf(GeoImageProcessConfiguration::class, $this->geoImageProcessConfiguration);
        $this->assertContains('inputFilename', $this->geoImageProcessConfiguration->getArguments());
        $this->assertContains('outputFilename', $this->geoImageProcessConfiguration->getArguments());

        $this->assertEquals('inputFilename', $this->geoImageProcessConfiguration->getInputFile());
        $this->assertEquals('outputFilename', $this->geoImageProcessConfiguration->getOutputFile());
        $this->assertEquals('geoimage/geoImageCreator.py', $this->geoImageProcessConfiguration->getScriptName());
        $this->assertTrue($this->geoImageProcessConfiguration->getIgnoreWarnings());
    }
}
