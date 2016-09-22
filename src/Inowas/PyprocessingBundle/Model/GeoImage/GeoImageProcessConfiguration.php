<?php

namespace Inowas\PyprocessingBundle\Model\GeoImage;

use Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;

class GeoImageProcessConfiguration extends PythonProcessConfiguration
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
     * GeoImageProcessConfiguration constructor.
     * @param ProcessFile $inputFile
     * @param ProcessFile $outputFile
     */
    public function __construct(ProcessFile $inputFile, ProcessFile $outputFile)
    {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->ignoreWarnings = true;
        $this->scriptName = 'geoimage/geoImageCreator.py';
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
