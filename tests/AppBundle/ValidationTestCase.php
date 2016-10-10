<?php

namespace Tests\AppBundle;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationTestCase extends \PHPUnit_Framework_TestCase
{

    /** @var  ValidatorInterface */
    protected $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = Validation::createValidatorBuilder()
            ->addXmlMapping(__DIR__.'/../../src/AppBundle/Resources/config/validation.xml')
            ->getValidator();
    }
}
