<?php

namespace Tests\AppBundle\Validation;

use AppBundle\Entity\ModFlowModel;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\AppBundle\ValidationTestCase;

class TestFlopyDisValidation extends ValidationTestCase
{
    public function testValidationOfAllElementsPass(){
        $stub = $this->getMockBuilder(ModFlowModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getNumberOfLayers')->willReturn(1);
        $stub->method('getNumberOfRows')->willReturn(1);
        $stub->method('getNumberOfColumns')->willReturn(1);
        $stub->method('getNumberOfStressPeriods')->willReturn(1);
        $stub->method('getSortedLayers')->willReturn(new ArrayCollection());
        $errors = $this->validator->validate($stub, null, array('flopyDis'));
        $this->assertCount(0, $errors);
    }

    public function testValidationOfAllElementsFail(){
        $stub = $this->getMockBuilder(ModFlowModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getNumberOfLayers')->willReturn(0);
        $stub->method('getNumberOfRows')->willReturn(0);
        $stub->method('getNumberOfColumns')->willReturn(0);
        $stub->method('getNumberOfStressPeriods')->willReturn(0);
        $stub->method('getSortedLayers')->willReturn(null);
        $errors = $this->validator->validate($stub, null, array('flopyDis'));
        $this->assertCount(5, $errors);
    }
}
