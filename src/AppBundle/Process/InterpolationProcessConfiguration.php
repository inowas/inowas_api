<?php

namespace AppBundle\Process;

class InterpolationProcessConfiguration extends PythonProcessConfiguration implements InterpolationProcessConfigurationInterface
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
     * @param ProcessFile $inputFile
     * @param ProcessFile $outputFile
     */
    public function __construct(ProcessFile $inputFile, ProcessFile $outputFile)
    {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->ignoreWarnings = true;
        $this->scriptName = 'interpolationCalculation.py';
    }

    /**
     * @param ProcessFile $file
     * @return $this
     */
    public function setInputFile(ProcessFile $file)
    {
        $this->inputFile = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputFile()
    {
        return $this->inputFile->getFileName();
    }

    /**
     * @param ProcessFile $file
     * @return mixed
     */
    public function setOutputFile(ProcessFile $file)
    {
        $this->outputFile = $file;
        return $this;
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