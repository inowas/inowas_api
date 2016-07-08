<?php

namespace AppBundle\Process;

interface InterpolationProcessConfigurationInterface extends PythonProcessConfigurationInterface
{
    /**
     * InterpolationProcessConfigurationInterface constructor.
     * @param ProcessFile $inputFile
     * @param ProcessFile $outputFile
     */
    public function __construct(ProcessFile $inputFile, ProcessFile $outputFile);

    /**
     * @param $file
     * @return $this
     */
    public function setInputFile(ProcessFile $file);

    /**
     * @return string
     */
    public function getInputFile();

    /**
     * @param $file
     * @return $this
     */
    public function setOutputFile(ProcessFile $file);

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

}