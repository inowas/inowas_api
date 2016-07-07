<?php

namespace AppBundle\Process;

interface ModflowConfigurationInterface extends PythonProcessConfigurationInterface
{
    /**
     * @param $fileName
     * @return $this
     */
    public function setInputFile($fileName);

    /**
     * @return string
     */
    public function getInputFile();

    /**
     * @param $fileName
     * @return $this
     */
    public function setOutputFile($fileName);

    /**
     * @return string
     */
    public function getOutputFile();

    /**
     * @param $directory
     * @return $this
     */
    public function setDataDirectory($directory);

    /**
     * @return $this
     */
    public function getDataDirectory();

    /**
     * @param $apiBaseUrl
     * @return $this
     */
    public function setAPIBaseUrl($apiBaseUrl);

    /**
     * @return string
     */
    public function getAPIBaseUrl();

    /**
     * @param $executable
     * @return $this
     */
    public function setExecutable($executable);

    /**
     * @return string
     */
    public function getExecutable();
}