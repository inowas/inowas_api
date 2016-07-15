<?php

namespace Inowas\PythonProcessBundle\Model;

interface InputOutputFileInterface
{
    public function getInputFile();

    public function getOutputFile();
}