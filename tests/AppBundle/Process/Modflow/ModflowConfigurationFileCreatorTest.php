<?php

namespace Tests\AppBundle\Process\Modflow;

use AppBundle\Process\Modflow\ModflowCalculationParameter;
use AppBundle\Process\Modflow\ModflowConfigurationFileCreator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class ModflowConfigurationFileCreatorTest extends WebTestCase
{

    /** @var  ModflowConfigurationFileCreator */
    protected $modflowConfigurationFileCreator;

    public function setUp(){
        self::bootKernel();
        $this->modflowConfigurationFileCreator = new ModflowConfigurationFileCreator(static::$kernel->getContainer()->get('kernel'));
    }

    public function testInstantiation(){
        $this->assertInstanceOf('AppBundle\Process\Modflow\ModflowConfigurationFileCreator', $this->modflowConfigurationFileCreator);
    }

    public function testCreateFile(){
        $this->modflowConfigurationFileCreator->createFiles(new ModflowCalculationParameter('123', '456'));
        $inputFile = $this->modflowConfigurationFileCreator->getInputFile();
        $outputFile = $this->modflowConfigurationFileCreator->getOutputFile();

        $this->assertFileExists($inputFile->getFileName());
        $this->assertFileExists($outputFile->getFileName());

        $fs = new Filesystem();
        $fs->remove(array($inputFile->getFileName(), $outputFile->getFileName()));

        $this->assertFileNotExists($inputFile->getFileName());
        $this->assertFileNotExists($outputFile->getFileName());
    }
}
