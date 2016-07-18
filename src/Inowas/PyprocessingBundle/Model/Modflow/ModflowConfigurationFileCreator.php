<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;

class ModflowConfigurationFileCreator
{

    /** @var  string */
    protected $tempFolder;

    /** @var  string */
    protected $dataFolder;

    /** @var  \Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile */
    protected $inputFile;

    /** @var  \Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile */
    protected $outputFile;

    /**
     * ModflowConfigurationFileCreator constructor.
     * @param $tempFolder
     */
    public function __construct($tempFolder)
    {
        $this->tempFolder = $tempFolder;
    }

    public function createFiles(ModflowParameterInterface $modflowCalculationParameter){

        $randomFileName = Uuid::uuid4()->toString();
        $inputFileName  = $this->tempFolder . '/' . $randomFileName . '.in';
        $outputFileName  = $this->tempFolder . '/' . $randomFileName . '.out';

        $fs = new Filesystem();
        $fs->dumpFile($inputFileName, json_encode($modflowCalculationParameter, JSON_UNESCAPED_SLASHES));
        $fs->touch($outputFileName);
        $this->inputFile = ProcessFile::fromFilename($inputFileName);
        $this->outputFile = ProcessFile::fromFilename($outputFileName);
    }

    /**
     * @return \Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile
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