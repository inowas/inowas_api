<?php

namespace Inowas\PyprocessingBundle\Model\PythonProcess;

interface InputOutputFileInterface
{
    public function getInputFile();

    public function getOutputFile();
}