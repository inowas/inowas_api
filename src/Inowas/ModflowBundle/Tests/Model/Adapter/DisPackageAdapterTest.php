<?php

namespace Inowas\ModflowBundle\Tests\Model\Adapter;

use Inowas\ModflowBundle\Model\Adapter\DisPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;
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
        $modflowModel = ModflowModelFactory::create();
        $soilmodel = SoilmodelFactory::create();

        $this->assertInstanceOf(DisPackageAdapter::class, new DisPackageAdapter($modflowModel, $soilmodel));
    }

    public function testValidation(){
        $modflowModel = ModflowModelFactory::create();
        $soilmodel = SoilmodelFactory::create();
        $dpa = new DisPackageAdapter($modflowModel, $soilmodel);
        $errors = $this->validator->validate($dpa);
        $this->assertGreaterThan(0, count($errors));
    }
}

