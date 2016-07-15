<?php

namespace Tests\Inowas\PythonProcessBundle\Model;

use Inowas\PythonProcessBundle\Model\ProcessFile;
use Symfony\Component\Filesystem\Filesystem;

class ProcessFileTest extends \PHPUnit_Framework_TestCase
{
    public function testFromFilenameInTheSameFolder(){
        $fs = new Filesystem();
        $fs->touch('./test.txt');
        $file = ProcessFile::fromFilename('test.txt');
        $this->assertInstanceOf('Inowas\PythonProcessBundle\Model\ProcessFile', $file);
        $this->assertEquals('test.txt', $file->getFileName());
        $fs->remove('test.txt');
    }

    public function testFromFilenameInAnotherFolder(){
        $fs = new Filesystem();
        $fs->touch('../test.txt');
        $file = ProcessFile::fromFilename('../test.txt');
        $this->assertInstanceOf('Inowas\PythonProcessBundle\Model\ProcessFile', $file);
        $this->assertEquals('../test.txt', $file->getFileName());
        $fs->remove('../test.txt');
    }

    public function testFromFilenameThrowsExceptionIfNotExits(){
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $file = ProcessFile::fromFilename('test.txt');
        $this->assertInstanceOf('Inowas\PythonProcessBundle\Model\ProcessFile', $file);
    }
}
