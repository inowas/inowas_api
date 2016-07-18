<?php

namespace Inowas\PyprocessingBundle\Tests\Model\GeoImage;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\RasterFactory;
use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageConfigurationFileCreator;
use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageParameter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class GeoImageConfigurationFileCreatorTest extends KernelTestCase
{

    /** @var  GeoImageConfigurationFileCreator */
    protected $geoImageConfigurationFileCreator;

    public function setUp(){
        self::bootKernel();
        $tempFolder = static::$kernel->getContainer()->getParameter('inowas.temp_folder');
        $dataFolder = static::$kernel->getContainer()->getParameter('inowas.geoimage.data_folder');

        $this->geoImageConfigurationFileCreator = new GeoImageConfigurationFileCreator($tempFolder, $dataFolder);
    }

    public function testInstantiation(){
        $this->assertInstanceOf(GeoImageConfigurationFileCreator::class, $this->geoImageConfigurationFileCreator);
    }

    public function testCreateFiles(){
        $this->geoImageConfigurationFileCreator->createFiles(new GeoImageParameter(RasterFactory::create()
            ->setBoundingBox(new BoundingBox())
            ->setGridSize(new GridSize())
        ));
        $inputFile = $this->geoImageConfigurationFileCreator->getInputFile();
        $outputFile = $this->geoImageConfigurationFileCreator->getOutputFile();

        $this->assertFileExists($inputFile->getFileName());

        $fs = new Filesystem();
        $fs->remove(array($inputFile->getFileName(), $outputFile->getFileName()));
        $this->assertFileNotExists($inputFile->getFileName());
    }
}
