<?php

namespace Inowas\PythonProcessBundle\Model;

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