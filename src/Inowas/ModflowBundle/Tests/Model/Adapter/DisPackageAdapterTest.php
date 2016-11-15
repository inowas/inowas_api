<?php

namespace Inowas\ModflowBundle\Tests\Model\Adapter;

use Inowas\ModflowBundle\Model\Adapter\DisPackageAdapter;
use Inowas\ModflowBundle\Model\ModFlowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DisPackageAdapterTest extends KernelTestCase
{

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function setUp()
    {
        self::bootKernel();
        $this->validator = static::$kernel->getContainer()
            ->get('validator')
        ;
    }

    public function testInstantiate(){
        /** @var ModFlowModel $stub */
        $stub = $this->getMockBuilder(ModFlowModel::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->assertInstanceOf(DisPackageAdapter::class, new DisPackageAdapter($stub));
    }

    public function testValidation(){
        $model = ModflowModelFactory::create();
        $dpa = new DisPackageAdapter($model);
        $errors = $this->validator->validate($dpa);
        $this->assertGreaterThan(0, count($errors));
    }

}

