<?php

namespace AppBundle\Process\Interpolation;

use AppBundle\Process\InputOutputFileInterface;
use AppBundle\Process\ProcessWithInputOutputFileInterface;
use AppBundle\Process\ProcessFile;
use AppBundle\Process\PythonProcessConfiguration;

class InterpolationProcessConfiguration extends PythonProcessConfiguration implements ProcessWithInputOutputFileInterface
{

    /**
     * @var ProcessFile
     */
    protected $inputFile;

    /**
     * @var ProcessFile
     */
    protected $outputFile;

    /**
     * InterpolationProcessConfiguration constructor.
     * @param InputOutputFileInterface $configurationFileCreator
     */
    public function __construct(InputOutputFileInterface $configurationFileCreator)
    {
        $this->inputFile = $configurationFileCreator->getInputFile();
        $this->outputFile = $configurationFileCreator->getOutputFile();
        $this->ignoreWarnings = true;
        $this->scriptName = 'interpolationCalculation.py';
    }

    /**
     * @return string
     */
    public function getInputFile()
    {
        return $this->inputFile->getFileName();
    }

    /**
     * @return mixed
     */
    public function getOutputFile()
    {
        return $this->outputFile->getFileName();
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        $this->arguments = parent::getArguments();
        $this->arguments[] = $this->getInputFile();
        $this->arguments[] = $this->getOutputFile();
        
        return $this->arguments;
    }
}