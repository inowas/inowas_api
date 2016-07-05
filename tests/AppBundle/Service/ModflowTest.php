<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\Modflow;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class ModflowTest extends WebTestCase
{
    /** @var KernelInterface $httpKernel */
    protected $httpKernel;

    /** @var Modflow $modflow */
    protected $modflow;

    /** @var Serializer $serializer */
    protected $serializer;

    public function setUp()
    {
        self::bootKernel();

        $this->modflow = static::$kernel->getContainer()
            ->get('inowas.modflow')
        ;

        $this->serializer = static::$kernel->getContainer()
            ->get('serializer')
        ;

        $this->httpKernel = static::$kernel->getContainer()
            ->get('kernel');

    }

    public function testIsDefaultDataDirectorySet()
    {
        $this->assertTrue(count($this->modflow->getDataFolder())>0);
        $this->assertContains('/app/../var/tmp', $this->modflow->getTmpFolder());
        $this->assertContains('/app/../var/data/modflow/123', $this->modflow->getWorkSpace('123'));
        $this->assertContains('/app/../py/pyprocessing/modflow', $this->modflow->getWorkingDirectory());
        $this->assertContains('/123', $this->modflow->getWorkSpace('123'));
    }

    public function testGetTmpFileName(){
        $this->assertTrue(Uuid::isValid($this->modflow->getTmpFileName()));
    }

    public function testGetInputFileName(){
        $uuid = Uuid::uuid4();
        $this->assertContains('/var/data/modflow/'.$uuid->toString(), $this->modflow->getWorkSpace($uuid));
    }

    public function testGetBaseUrlInTestMode(){
        $this->assertContains('http://localhost/', $this->modflow->getBaseUrl());
    }

    public function testCalculationWithUnknownExecutableThrowsExcebption(){
        $this->setExpectedException('InvalidArgumentException');
        $this->modflow->calculate(Uuid::uuid4(), 'sdklfj');
    }

    public function testCalculateCreatesTmpFolderAndCalculationFile(){
        $processStub = $this->getMockBuilder(Process::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setArguments', 'setWorkingDirectory', 'getProcess', 'isSuccessful', 'run', 'getOutput'))
            ->getMock()
        ;

        $processStub->method('isSuccessful')->willReturn(true);
        $processStub->method('setArguments')->willReturn($processStub);
        $processStub->method('setWorkingDirectory')->willReturn($processStub);
        $processStub->method('getProcess')->willReturn($processStub);
        $processStub->method('getOutput')->willReturn('{"error":"Exception raised in calculation of method gaussian"}');

        $modflow = new Modflow($this->serializer, $this->httpKernel, $processStub, 'workingdir', 'dataFolder', 'tempFolder', 'baseUrl');

        $fs = new Filesystem();
        if ($fs->exists($modflow->getTmpFolder())) {
            $fs->remove($modflow->getTmpFolder());
        }
        $this->assertFalse($fs->exists($modflow->getTmpFolder()));
        $modelId = Uuid::uuid4();

        $modflow->calculate($modelId);
        $this->assertTrue($fs->exists($modflow->getTmpFolder()));
        $this->assertTrue($fs->exists($modflow->getTmpFolder().'/'.$modflow->getTmpFileName().'.in'));

        if ($fs->exists($modflow->getTmpFolder())) {
            $fs->remove($modflow->getTmpFolder());
        }
    }

    public function testCalculationThrowsExceptionIfProcessIsNotSuccessful(){
        $processStub = $this->getMockBuilder(Process::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setArguments', 'setWorkingDirectory', 'getProcess', 'isSuccessful', 'run', 'getOutput'))
            ->getMock()
        ;

        $processStub->method('isSuccessful')->willReturn(false);
        $processStub->method('setArguments')->willReturn($processStub);
        $processStub->method('setWorkingDirectory')->willReturn($processStub);
        $processStub->method('getProcess')->willReturn($processStub);
        $processStub->method('run')->willReturn($processStub);
        $processStub->method('getOutput')->willReturn('{"error":"Exception raised in calculation of method gaussian"}');

        $this->setExpectedException('AppBundle\Exception\ProcessFailedException');
        $modflow = new Modflow($this->serializer, $this->httpKernel, $processStub, 'workingdir', 'dataFolder', 'tempFolder', 'baseUrl');
        $modelId = Uuid::uuid4();
        $modflow->calculate($modelId);

        $fs = new Filesystem();
        if ($fs->exists($modflow->getTmpFolder())) {
            $fs->remove($modflow->getTmpFolder());
        }
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        if ($fs->exists('tempFolder')) {
            $fs->remove('tempFolder');
        }
    }
}