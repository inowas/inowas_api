<?php

namespace AppBundle\Process\GeoImage;

use AppBundle\Model\GeoImage\GeoImageProperties;
use Inowas\PythonProcessBundle\Model\InputOutputFileInterface;
use Inowas\PythonProcessBundle\Model\ProcessFile;
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
     * GeoImageConfigurationFileCreator constructor.
     * @param KernelInterface $kernel
     * @param $serializer
     */
    public function __construct(KernelInterface $kernel, $serializer)
    {
        $this->tempFolder = $kernel->getContainer()->getParameter('inowas.temp_folder');
        $this->dataFolder = $kernel->getContainer()->getParameter('inowas.geotiff.data_folder');
        $this->serializer = $serializer;
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

        $json = $this->serializer->serialize(
            $geoImageProperties,
            'json',
            SerializationContext::create()->setGroups(array("geoimage"))
        );

        $randomFileName = Uuid::uuid4()->toString();
        $inputFileName  = $this->tempFolder . '/' . $randomFileName . '.in';
        $outputFileName = $this->dataFolder.'/'.$parameter->getRaster()->getId()->toString().'.'.$parameter->getFileFormat();

        $fs = new Filesystem();
        $fs->dumpFile($inputFileName, $json);

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