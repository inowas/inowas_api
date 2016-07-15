<?php

namespace Inowas\PythonProcessBundle\Model;

interface PythonProcessConfigurationInterface
{
    /**
     * @param $arguments
     * @return $this
     */
    public function setArguments(array $arguments);

    /**
     * @return array
     */
    public function getArguments();

    /**
     * @param boolean $ignoreWarnings
     * @return $this
     */
    public function setIgnoreWarnings($ignoreWarnings);

    /**
     * @return bool
     */
    public function getIgnoreWarnings();

    /**
     * @param $scriptName
     * @return $this
     */
    public function setScriptName($scriptName);

    /**
     * @return string
     */
    public function getScriptName();

    /**
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * @return string
     */
    public function getPrefix();

    /**
     * @param $workingDirectory
     * @return $this
     */
    public function setWorkingDirectory($workingDirectory);

    /**
     * @return string
     */
    public function getWorkingDirectory();
}