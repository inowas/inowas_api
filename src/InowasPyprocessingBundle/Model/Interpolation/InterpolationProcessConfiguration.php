<?php

namespace InowasPyprocessingBundle\Model\Interpolation;

use InowasPyprocessingBundle\Model\PythonProcess\InputOutputFileInterface;
use InowasPyprocessingBundle\Model\PythonProcess\ProcessWithInputOutputFileInterface;
use InowasPyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;

class InterpolationProcessConfiguration extends PythonProcessConfiguration implements ProcessWithInputOutputFileInterface
{

    /**
     * @var \InowasPyprocessingBundle\Model\PythonProcess\ProcessFile
     */
    protected $inputFile;

    /**
     * @var \InowasPyprocessingBundle\Model\PythonProcess\ProcessFile
     */
    protected $outputFile;

    /**
     * InterpolationProcessConfiguration constructor.
     * @param \InowasPyprocessingBundle\Model\PythonProcess\InputOutputFileInterface $configurationFileCreator
     */
    public function __construct(InputOutputFileInterface $configurationFileCreator)
    {
        $this->inputFile = $configurationFileCreator->getInputFile();
        $this->outputFile = $configurationFileCreator->getOutputFile();
        $this->ignoreWarnings = true;
        $this->scriptName = 'interpolation/interpolationCalculation.py';
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