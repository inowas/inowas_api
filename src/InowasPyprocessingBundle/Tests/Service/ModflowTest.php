<?php

namespace InowasPyprocessingBundle\Tests\Service;

use AppBundle\Entity\ModFlowModel;
use Doctrine\ORM\EntityManager;
use InowasPyprocessingBundle\Service\Modflow;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ModflowTest
 * @package Tests\AppBundle\Service
 *
 * @group integration_tests
 */
class ModflowTest extends WebTestCase
{
    /** @var KernelInterface $httpKernel */
    protected $httpKernel;

    /** @var Modflow $modflow */
    protected $modflow;

    /** @var  EntityManager */
    protected $entityManager;

    public function setUp()
    {
        self::bootKernel(array('environment' => 'dev'));

        $this->modflow = static::$kernel->getContainer()
            ->get('inowas.modflow')
        ;

        $this->httpKernel = static::$kernel->getContainer()
            ->get('kernel');

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');

    }

    public function testIsTestModelAvailable()
    {
        $model = $this->entityManager->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'name' => "Lake Example"
            ));

        if (! $model instanceof ModFlowModel){
            $this->assertTrue(false);
        }
    }

    public function testCalculate(){
        $model = $this->entityManager->getRepository('AppBundle:ModFlowModel')->findOneBy(array('name' => "Lake Example"));
        $this->modflow->calculate($model->getId()->toString());
    }
}