<?php

namespace AppBundle\Process\GeoImage;

use Inowas\PythonProcessBundle\Model\InputOutputFileInterface;
use Inowas\PythonProcessBundle\Model\ProcessWithInputOutputFileInterface;
use Inowas\PythonProcessBundle\Model\ProcessFile;
use Inowas\PythonProcessBundle\Model\PythonProcessConfiguration;

class GeoImageProcessConfiguration extends PythonProcessConfiguration implements ProcessWithInputOutputFileInterface
{

    /**
     * @var \Inowas\PythonProcessBundle\Model\ProcessFile
     */
    protected $inputFile;

    /**
     * @var ProcessFile
     */
    protected $outputFile;

    /**
     * InterpolationProcessConfiguration constructor.
     * @param \Inowas\PythonProcessBundle\Model\InputOutputFileInterface $configurationFileCreator
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