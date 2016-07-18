<?php

namespace InowasPyprocessingBundle\Model\PythonProcess;

interface InputOutputFileInterface
{
    public function getInputFile();

    public function getOutputFile();
}