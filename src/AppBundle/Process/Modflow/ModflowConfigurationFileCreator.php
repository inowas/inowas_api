<?php

namespace AppBundle\Process\Modflow;

use AppBundle\Process\ProcessFile;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class ModflowConfigurationFileCreator
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
     * InterpolationConfigurationFileCreator constructor.
     * @param $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->tempFolder = $kernel->getContainer()->getParameter('inowas.temp_folder');
        $this->dataFolder = $kernel->getContainer()->getParameter('inowas.modflow.data_folder');
    }

    public function createFiles(ModflowParameterInterface $modflowCalculationParameter){

        $randomFileName = Uuid::uuid4()->toString();
        $inputFileName  = $this->tempFolder . '/' . $randomFileName . '.in';
        $outputFileName  = $this->tempFolder . '/' . $randomFileName . '.out';

        $fs = new Filesystem();
        $fs->dumpFile($inputFileName, json_encode($modflowCalculationParameter, JSON_UNESCAPED_SLASHES));
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