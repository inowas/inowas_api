<?php

namespace Tests\AppBundle\Validation;

use AppBundle\Model\ModFlowModelFactory;
use Tests\AppBundle\ValidationTestCase;

class TestFlopyMfValidation extends ValidationTestCase
{
    public function testValidationOfModelName(){
        $model = ModFlowModelFactory::create();
        $model->setName("");

        $errors = $this->validator->validate($model, null, array('flopyMf'));
        $this->assertCount(1, $errors);

        $model->setName("abc");
        $errors = $this->validator->validate($model, null, array('flopyMf'));
        $this->assertCount(0, $errors);
    }
}
