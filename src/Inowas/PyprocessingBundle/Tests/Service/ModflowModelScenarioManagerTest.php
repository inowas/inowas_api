<?php

namespace Inowas\PyprocessingBundle\Tests\Service;


use AppBundle\Service\GeoTools;
use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Service\ModflowModelScenarioManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModflowModelScenarioManagerTest extends KernelTestCase
{
    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var GeoTools $geoTools
     */
    protected $geoTools;

    /**
     * @var ModflowModelScenarioManager
     */
    protected $modflowModelScenarioManager;

    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->geoTools = static::$kernel->getContainer()
            ->get('inowas.geotools');

        $this->modflowModelScenarioManager = new ModflowModelScenarioManager($this->em, $this->geoTools);
    }

    public function testInstantiate(){
        $this->assertInstanceOf(ModflowModelScenarioManager::class, $this->modflowModelScenarioManager);
    }
}
