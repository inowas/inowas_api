<?php

namespace InowasPyprocessingBundle\Tests\Model\Modflow;

use InowasPyprocessingBundle\Model\Modflow\ModflowCalculationParameter;
use InowasPyprocessingBundle\Model\Modflow\ModflowConfigurationFileCreator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class ModflowConfigurationFileCreatorTest extends KernelTestCase
{

    /** @var  ModflowConfigurationFileCreator */
    protected $modflowConfigurationFileCreator;

    public function setUp(){
        self::bootKernel();
        $tempFolder = static::$kernel->getContainer()->getParameter('inowas.temp_folder');
        $this->modflowConfigurationFileCreator = new ModflowConfigurationFileCreator($tempFolder);
    }

    public function testInstantiation(){
        $this->assertInstanceOf(ModflowConfigurationFileCreator::class, $this->modflowConfigurationFileCreator);
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
