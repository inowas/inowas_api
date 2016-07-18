<?php

namespace Inowas\PyprocessingBundle\Model\PythonProcess;

class PythonProcessConfiguration implements PythonProcessConfigurationInterface
{
    /**
     * @var array $arguments
     */
    protected $arguments = array();

    /**
     * @var string
     */
    protected $prefix = 'python';

    /**
     * @var bool
     */
    protected $ignoreWarnings = true;

    /**
     * @var string
     */
    protected $scriptName = '';

    /**
     * @var string
     */
    protected $workingDirectory = '../py/pyprocessing';

    /**
     * @param $arguments
     * @return mixed
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return boolean
     */
    public function getIgnoreWarnings()
    {
        return $this->ignoreWarnings;
    }

    /**
     * @param boolean $ignoreWarnings
     * @return PythonProcessConfiguration
     */
    public function setIgnoreWarnings($ignoreWarnings)
    {
        $this->ignoreWarnings = $ignoreWarnings;
        return $this;
    }

    /**
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }

    /**
     * @param string $scriptName
     * @return PythonProcessConfiguration
     */
    public function setScriptName($scriptName)
    {
        $this->scriptName = $scriptName;
        return $this;
    }

    /**
     * @param $workingDirectory
     * @return $this
     */
    public function setWorkingDirectory($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
        return $this;
    }

    /**
     * @return string
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }
}