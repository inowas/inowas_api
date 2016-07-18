<?php

namespace InowasPyprocessingBundle\Tests\Model\PythonProcess;

use InowasPyprocessingBundle\Exception\InvalidArgumentException;
use InowasPyprocessingBundle\Model\PythonProcess\ProcessFile;
use Symfony\Component\Filesystem\Filesystem;

class ProcessFileTest extends \PHPUnit_Framework_TestCase
{
    public function testFromFilenameInTheSameFolder(){
        $fs = new Filesystem();
        $fs->touch('./test.txt');
        $file = ProcessFile::fromFilename('test.txt');
        $this->assertInstanceOf(ProcessFile::class, $file);
        $this->assertEquals('test.txt', $file->getFileName());
        $fs->remove('test.txt');
    }

    public function testFromFilenameInAnotherFolder(){
        $fs = new Filesystem();
        $fs->touch('../test.txt');
        $file = ProcessFile::fromFilename('../test.txt');
        $this->assertInstanceOf(ProcessFile::class, $file);
        $this->assertEquals('../test.txt', $file->getFileName());
        $fs->remove('../test.txt');
    }

    public function testFromFilenameThrowsExceptionIfNotExits(){
        $this->setExpectedException(InvalidArgumentException::class);
        $file = ProcessFile::fromFilename('test.txt');
        $this->assertInstanceOf(ProcessFile::class, $file);
    }
}
