<?php

namespace AppBundle\Process\GeoImage;

use AppBundle\Process\InputOutputFileInterface;
use AppBundle\Process\ProcessWithInputOutputFileInterface;
use AppBundle\Process\ProcessFile;
use AppBundle\Process\PythonProcessConfiguration;

class GeoImageProcessConfiguration extends PythonProcessConfiguration implements ProcessWithInputOutputFileInterface
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
        $this->scriptName = 'geoImageCreator.py';
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