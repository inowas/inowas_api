<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;

class ModflowResultProcessConfiguration extends PythonProcessConfiguration
{

    /** @var \Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile */
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