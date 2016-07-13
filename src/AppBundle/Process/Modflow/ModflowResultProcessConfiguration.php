<?php

namespace AppBundle\Process\Modflow;

use AppBundle\Process\ProcessFile;
use AppBundle\Process\PythonProcessConfiguration;

class ModflowResultProcessConfiguration extends PythonProcessConfiguration
{

    /** @var ProcessFile */
    protected $inputFile;

    /** @var ProcessFile */
    protected $outputFile;

    /** @var string */
    protected $workspace;

    /** @var string */
    protected $baseUrl;

    /**
     * ModflowResultProcessConfiguration constructor.
     * @param ProcessFile $inputFile
     * @param ProcessFile $outputFile
     * @param $workspace
     * @param $baseUrl
     */
    public function __construct(ProcessFile $inputFile, ProcessFile $outputFile, $workspace, $baseUrl)
    {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->workspace = $workspace;
        $this->baseUrl = $baseUrl;

        $this->ignoreWarnings = true;
        $this->scriptName = 'modflowResult.py';
    }

    /**
     * @return string
     */
    public function getInputFile()
    {
        return $this->inputFile->getFileName();
    }

    /**
     * @return string
     */
    public function getOutputFile()
    {
        return $this->outputFile->getFileName();
    }

    /**
     * @return string
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        $this->arguments = parent::getArguments();
        $this->arguments[] = $this->baseUrl;
        $this->arguments[] = $this->workspace;
        $this->arguments[] = $this->inputFile->getFileName();
        $this->arguments[] = $this->outputFile->getFileName();
        return $this->arguments;
    }
}