<?php

namespace Inowas\PythonProcessBundle\Model;

use AppBundle\Exception\InvalidArgumentException;
use Inowas\PythonProcessBundle\Model\ModflowProcessConfigurationInterface;

class ModflowProcessConfiguration extends PythonProcessConfiguration implements ModflowProcessConfigurationInterface
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
     * @var ProcessFile
     */
    protected $inputFile;

    /**
     * @var ProcessFile
     */
    protected $outputFile;

    /**
     * @var string
     */
    protected $dataDirectory;

    public function __construct(ProcessFile $inputFile)
    {
        $this->inputFile = $inputFile;
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
     * @return $this
     */
    public function setOutputFile(ProcessFile $file)
    {
        $this->outputFile = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutputFile()
    {
        return $this->outputFile->getFileName();
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
            throw new InvalidArgumentException(sprintf('Executable %s not available', $executable));
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
        $this->arguments = parent::getArguments();
        $this->arguments[] = $this->getAPIBaseUrl();
        $this->arguments[] = $this->getExecutable();
        $this->arguments[] = $this->getDataDirectory();
        $this->arguments[] = $this->getInputFile();
        return $this->arguments;
    }
}