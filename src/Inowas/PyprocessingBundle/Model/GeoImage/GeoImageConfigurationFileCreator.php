<?php

namespace Inowas\PyprocessingBundle\Model\GeoImage;

use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageProperties;
use Inowas\PyprocessingBundle\Model\PythonProcess\InputOutputFileInterface;
use Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;

class GeoImageConfigurationFileCreator implements InputOutputFileInterface
{
    /** @var  string */
    protected $tempFolder;
    
    /** @var  string */
    protected $dataFolder;

    /** @var  ProcessFile */
    protected $inputFile;

    /** @var  ProcessFile */
    protected $outputFile;

    /**
     * GeoImageConfigurationFileCreator constructor.
     * @param $tempFolder
     * @param $dataFolder
     */
    public function __construct($tempFolder, $dataFolder)
    {
        $this->tempFolder = $tempFolder;
        $this->dataFolder = $dataFolder;
    }

    public function createFiles(GeoImageParameter $parameter){

        $geoImageProperties = new GeoImageProperties(
            $parameter->getRaster(),
            $parameter->getActiveCells(),
            $parameter->getColorRelief(),
            $parameter->getTargetProjection(),
            $parameter->getFileFormat(),
            $parameter->getMin(),
            $parameter->getMax()
        );

        $randomFileName = Uuid::uuid4()->toString();
        $inputFileName  = $this->tempFolder . '/' . $randomFileName . '.in';
        $outputFileName = $this->dataFolder.'/'.$parameter->getRaster()->getId()->toString().'.'.$parameter->getFileFormat();

        $fs = new Filesystem();
        $fs->dumpFile($inputFileName, json_encode($geoImageProperties));

        $this->inputFile = ProcessFile::fromFilename($inputFileName);
        $this->outputFile = ProcessFile::fromFilename($outputFileName, false);
    }

    /**
     * @return ProcessFile
     */
    public function getInputFile()
    {
        return $this->inputFile;
    }

    /**
     * @return ProcessFile
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }
}
