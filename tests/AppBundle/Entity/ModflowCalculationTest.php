<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\ModflowCalculation;
use Ramsey\Uuid\Uuid;

class ModflowCalculationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModflowCalculation */
    protected $modflowCalculation;

    public function setUp()
    {
        $this->modflowCalculation = new ModflowCalculation();
    }

    public function testInstantiate(){
        $this->assertInstanceOf(ModflowCalculation::class, $this->modflowCalculation);
        $this->assertInstanceOf(Uuid::class, $this->modflowCalculation->getId());
        $this->assertInstanceOf(\DateTime::class, $this->modflowCalculation->getDateTimeAddToQueue());
        $this->assertEquals(ModflowCalculation::STATE_IN_QUEUE, $this->modflowCalculation->getState());
    }

    public function testSetterGetter(){
        $processId = Uuid::uuid4();
        $this->modflowCalculation->setProcessId($processId);
        $this->assertEquals($processId, $this->modflowCalculation->getProcessId());

        $modelId = Uuid::uuid4();
        $this->modflowCalculation->setModelId($modelId);
        $this->assertEquals($modelId, $this->modflowCalculation->getModelId());

        $userId = Uuid::uuid4();
        $this->modflowCalculation->setModelId($userId);
        $this->assertEquals($userId, $this->modflowCalculation->getModelId());

        $baseUrl = 'baseUrl';
        $this->modflowCalculation->setOutput($baseUrl);
        $this->assertEquals($baseUrl, $this->modflowCalculation->getOutput());

        $dataFolder = 'dataFolder';
        $this->modflowCalculation->setOutput($dataFolder);
        $this->assertEquals($dataFolder, $this->modflowCalculation->getOutput());

        $state = ModflowCalculation::STATE_FINISHED_SUCCESSFUL;
        $this->modflowCalculation->setState($state);
        $this->assertEquals($state, $this->modflowCalculation->getState());

        $dateTimeStart = new \DateTime('2015-01-01');
        $this->modflowCalculation->setDateTimeStart($dateTimeStart);
        $this->assertEquals($dateTimeStart, $this->modflowCalculation->getDateTimeStart());

        $dateTimeEnd = new \DateTime('2016-01-01');
        $this->modflowCalculation->setDateTimeEnd($dateTimeEnd);
        $this->assertEquals($dateTimeEnd, $this->modflowCalculation->getDateTimeEnd());

        $output = 'output';
        $this->modflowCalculation->setOutput($output);
        $this->assertEquals($output, $this->modflowCalculation->getOutput());

        $errorOutput = 'errorOutput';
        $this->modflowCalculation->setErrorOutput($errorOutput);
        $this->assertEquals($errorOutput, $this->modflowCalculation->getErrorOutput());
    }

}
