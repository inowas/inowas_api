<?php

namespace Inowas\PyprocessingBundle\Tests\Service;

use AppBundle\Entity\ModflowCalculation;
use AppBundle\Entity\ModFlowModel;
use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Service\Flopy;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FlopyTest
 *
 * @group integration_tests
 */
class FlopyTest extends WebTestCase
{
    /** @var  EntityManager */
    protected $entityManager;

    /** @var Flopy */
    protected $flopy;

    /** @var string  */
    protected $dataFolder = __DIR__.'/../TestData/Modflow';

    public function setUp()
    {
        self::bootKernel(array('environment' => 'dev'));

        $this->flopy = static::$kernel->getContainer()
            ->get('inowas.flopy')
        ;

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');
    }

    public function testIsTestModelAvailable()
    {
        $model = $this->entityManager->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'name' => "Lake_Example"
            ));

        if (! $model instanceof ModFlowModel){
            $this->assertTrue(false);
        }
    }

    public function testCalculate(){
        $model = $this->entityManager->getRepository('AppBundle:ModFlowModel')->findOneBy(array('name' => "Lake_Example"));
        $processExecutedSuccessfully = $this->flopy->calculate(
            'http://localhost/api',
            $this->dataFolder,
            $model->getId()->toString(),
            $model->getOwner()->getApiKey()
        );

        $this->assertFileExists($this->dataFolder.'/'.$model->getId()->toString().'/ascii/'.$model->getName().'.nam');
        $this->assertTrue($processExecutedSuccessfully);
    }

    public function testAddToQueue(){
        $model = $this->entityManager->getRepository('AppBundle:ModFlowModel')->findOneBy(array('name' => "Lake_Example"));
        $modflowCalculation = $this->flopy->addModelToQueue(
            'http://localhost/api',
            $this->dataFolder,
            $model->getId()->toString(),
            $model->getOwner()->getId()->toString()
        );

        $this->assertInstanceOf(ModflowCalculation::class, $modflowCalculation);
    }


    public function tearDown() {
        if (file_exists($this->dataFolder)){
            $this->removeDirectory($this->dataFolder);
        }
    }

    private function removeDirectory($path) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
        return;
    }
}