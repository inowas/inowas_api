<?php

namespace AppBundle\Process;

interface ConfigurationFileCreatorInterface
{
    public function getInputFile();

    public function getOutputFile();
}