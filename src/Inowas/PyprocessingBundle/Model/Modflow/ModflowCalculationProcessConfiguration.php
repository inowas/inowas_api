<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;

class ModflowCalculationProcessConfiguration extends PythonProcessConfiguration
{

    /** @var ProcessFile */
    protected $inputFile;

    /** @var string */
    protected $workspace;

    /** @var  string */
    protected $baseUrl;

    /** @var  string */
    protected $executable;

    /**
     * ModflowCalculationProcessConfiguration constructor.
     * @param ProcessFile $inputFile
     * @param $workspace
     * @param $executable
     * @param $baseUrl
     */
    public function __construct(ProcessFile $inputFile, $workspace, $executable, $baseUrl)
    {
        $this->inputFile = $inputFile;
        $this->workspace = $workspace;
        $this->executable = $executable;
        $this->baseUrl = $baseUrl;

        $this->ignoreWarnings = true;
        $this->scriptName = 'modflow/modflowCalculation.py';
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        $this->arguments = parent::getArguments();
        $this->arguments[] = $this->baseUrl;
        $this->arguments[] = $this->executable;
        $this->arguments[] = $this->workspace;
        $this->arguments[] = $this->inputFile->getFileName();
        return $this->arguments;
    }
}