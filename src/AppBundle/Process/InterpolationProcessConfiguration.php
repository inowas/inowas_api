<?php

namespace AppBundle\Process;

class InterpolationProcessConfiguration extends PythonProcessConfiguration implements ProcessConfigurationInterface
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
     * @param ConfigurationFileCreatorInterface $configurationFileCreator
     */
    public function __construct(ConfigurationFileCreatorInterface $configurationFileCreator)
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