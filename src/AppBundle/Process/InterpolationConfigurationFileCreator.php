<?php

namespace AppBundle\Process;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;

class InterpolationConfigurationFileCreator implements ConfigurationFileCreatorInterface
{

    /** @var  Serializer */
    protected $serializer;

    /** @var  string */
    protected $tempFolder;

    /** @var  ProcessFile */
    protected $inputFile;

    /** @var  ProcessFile */
    protected $outputFile;

    /**
     * InterpolationConfigurationFileCreator constructor.
     * @param $tempFolder
     * @param Serializer $serializer
     */
    public function __construct($tempFolder, Serializer $serializer)
    {
        $this->tempFolder = $tempFolder;
        $this->serializer = $serializer;
    }

    public function createFiles($algorithm, InterpolationParameter $interpolationParameter){

        $class = 'AppBundle\Model\Interpolation\\' . ucfirst($algorithm) . 'Interpolation';
        $interpolation = new $class($interpolationParameter);

        $interpolationJSON = $this->serializer->serialize(
            $interpolation,
            'json',
            SerializationContext::create()->setGroups(array('interpolation'))
        );

        $randomFileName = Uuid::uuid4()->toString();
        $inputFileName  = $this->tempFolder . '/' . $randomFileName . '.in';
        $outputFileName = $this->tempFolder . '/' . $randomFileName . '.out';

        $fs = new Filesystem();
        $fs->dumpFile($inputFileName, $interpolationJSON);
        $fs->touch($outputFileName);

        $this->inputFile = ProcessFile::fromFilename($inputFileName);
        $this->outputFile = ProcessFile::fromFilename($outputFileName);
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