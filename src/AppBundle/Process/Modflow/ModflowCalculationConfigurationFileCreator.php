<?php

namespace AppBundle\Process\Modflow;

use AppBundle\Process\InputFileWorkspaceInterface;
use AppBundle\Process\ProcessFile;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class ModflowCalculationConfigurationFileCreator implements InputFileWorkspaceInterface
{

    /** @var  string */
    protected $tempFolder;

    /** @var  string */
    protected $dataFolder;

    /** @var  ProcessFile */
    protected $inputFile;

    /**
     * InterpolationConfigurationFileCreator constructor.
     * @param $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->tempFolder = $kernel->getContainer()->getParameter('inowas.temp_folder');
        $this->dataFolder = $kernel->getContainer()->getParameter('inowas.modflow.data_folder');
    }

    public function createFiles(ModflowCalculationParameterInterface $modflowCalculationParameter){

        $modflowCalculationInput = new ModflowCalculationInput(
            $modflowCalculationParameter->getModelId(),
            $modflowCalculationParameter->getBaseUrl()
        );

        $randomFileName = Uuid::uuid4()->toString();
        $inputFileName  = $this->tempFolder . '/' . $randomFileName . '.in';

        $fs = new Filesystem();
        $fs->dumpFile($inputFileName, json_encode($modflowCalculationInput, JSON_UNESCAPED_SLASHES));
        $this->inputFile = ProcessFile::fromFilename($inputFileName);
    }

    /**
     * @return ProcessFile
     */
    public function getInputFile()
    {
        return $this->inputFile;
    }

    /**
     * @param ModflowCalculationParameterInterface $modflowCalculationParameter
     * @return string
     */
    public function getWorkspace(ModflowCalculationParameterInterface $modflowCalculationParameter)
    {
        return $this->dataFolder.'/'.$modflowCalculationParameter->getModelId();
        
    }


}