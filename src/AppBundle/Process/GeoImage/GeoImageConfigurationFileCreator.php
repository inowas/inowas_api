<?php

namespace AppBundle\Process\GeoImage;

use AppBundle\Process\InputOutputFileInterface;
use AppBundle\Process\InterpolationParameter;
use AppBundle\Process\ProcessFile;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class GeoImageConfigurationFileCreator implements InputOutputFileInterface
{

    /** @var  Serializer */
    protected $serializer;

    /** @var  string */
    protected $tempFolder;
    
    /** @var  string */
    protected $dataFolder;

    /** @var  ProcessFile */
    protected $inputFile;

    /** @var  ProcessFile */
    protected $outputFile;

    /**
     * InterpolationConfigurationFileCreator constructor.
     * @param $kernel
     * @param $serializer
     */
    public function __construct(KernelInterface $kernel, $serializer)
    {
        $this->tempFolder = $kernel->getContainer()->getParameter('inowas.temp_folder');
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