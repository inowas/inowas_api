<?php

namespace Inowas\PyprocessingBundle\Model\PythonProcess;

use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;

class ProcessFile
{
    private $fileName;

    private final function __construct(){}

    public static function fromFilename($fileName, $assertFileExists = true)
    {
        $fs = new Filesystem();
        if ($assertFileExists && ! $fs->exists($fileName)){
            throw new InvalidArgumentException(sprintf('File %s not exists.', $fileName));
        }

        $instance = new self();
        $instance->fileName = $fileName;
        return $instance;
    }

    public function getFileName()
    {
        return $this->fileName;
    }
}