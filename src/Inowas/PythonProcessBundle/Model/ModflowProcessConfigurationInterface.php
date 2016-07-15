<?php

namespace Inowas\PythonProcessBundle\Model;

use Inowas\PythonProcessBundle\Model\ProcessFile;
use Inowas\PythonProcessBundle\Model\PythonProcessConfigurationInterface;

interface ModflowProcessConfigurationInterface extends PythonProcessConfigurationInterface
{
    /**
     * ModflowProcessConfigurationInterface constructor.
     * @param ProcessFile $inputFile
     */
    public function __construct(ProcessFile $inputFile);

    /**
     * @param $fileName
     * @return $this
     */
    public function setInputFile(ProcessFile $fileName);

    /**
     * @return string
     */
    public function getInputFile();

    /**
     * @param $fileName
     * @return $this
     */
    public function setOutputFile(ProcessFile $fileName);

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