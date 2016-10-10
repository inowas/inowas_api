<?php

namespace Inowas\PyprocessingBundle\Model\PythonProcess;

interface PythonProcessConfigurationInterface
{
    /**
     * @return array
     */
    public function getArguments();

    /**
     * @return bool
     */
    public function getIgnoreWarnings();

    /**
     * @return string
     */
    public function getScriptName();

    /**
     * @return string
     */
    public function getPrefix();

    /**
     * @return string
     */
    public function getWorkingDirectory();
}
