<?php

namespace AppBundle\Process;

class ModflowProcessConfiguration extends PythonProcessConfiguration implements ModflowConfigurationInterface
{

    const MODFLOW_2005  = "mf2005";
    const MODFLOW_NWT   = "mfnwt";

    /** @var array */
    protected $availableExecutables = [self::MODFLOW_2005];

    /**
     * @var string
     */
    protected $apiBaseUrl ='http://localhost/';

    /**
     * @var string
     */
    protected $executable = 'mf2005';

    /**
     * @var string
     */
    protected $inputFile;

    /**
     * @var string
     */
    protected $outputFile;

    /**
     * @var string
     */
    protected $dataDirectory;

    public function __construct()
    {
        $this->ignoreWarnings = true;
        $this->scriptName = 'modflowCalculation.py';
    }

    /**
     * @param $apiBaseUrl
     * @return mixed
     */
    public function setAPIBaseUrl($apiBaseUrl)
    {
        $this->apiBaseUrl = $apiBaseUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAPIBaseUrl()
    {
        return $this->apiBaseUrl;
    }

    /**
     * @param $fileName
     * @return mixed
     */
    public function setInputFile($fileName)
    {
        $this->inputFile = $fileName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInputFile()
    {
        return $this->inputFile;
    }

    /**
     * @param $fileName
     * @return mixed
     */
    public function setOutputFile($fileName)
    {
        $this->outputFile = $fileName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * @param $directory
     * @return $this
     */
    public function setDataDirectory($directory)
    {
        $this->dataDirectory = $directory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataDirectory()
    {
        return $this->dataDirectory;
    }

    /**
     * @param $executable
     * @return $this
     */
    public function setExecutable($executable)
    {
        if (! in_array($executable, $this->availableExecutables)){
            throw new \InvalidArgumentException(sprintf('Executable %s not available', $executable));
        }
        $this->executable = $executable;
        return $this;
    }

    /**
     * @return string
     */
    public function getExecutable()
    {
        return $this->executable;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return array(
            $this->getAPIBaseUrl(),
            $this->getExecutable(),
            $this->getDataDirectory(),
            $this->getInputFile()
        );
    }


}