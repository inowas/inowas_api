<?php

namespace Inowas\ModflowBundle\Tests\Service;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Service\HeadsManager;
use Inowas\ModflowBundle\Service\ModflowToolManager;
use Inowas\PyprocessingBundle\Tests\Service\ModflowModelManagerTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HeadsManagerTest extends WebTestCase
{
    /** @var  EntityManager */
    protected $em;

    /** @var HeadsManager */
    protected $hh;

    /** @var ModflowToolManager */
    protected $mm;

    public function setUp()
    {
        self::bootKernel();
        $this->hh = static::$kernel->getContainer()
            ->get('inowas.modflow.headsmanager')
        ;

        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->mm = static::$kernel->getContainer()
            ->get('inowas.modflow.toolmanager')
        ;
    }

    public function testAddHead()
    {
        $model = $this->mm->createModel();
        $totim = 12.111;
        $headData = [
            [1,2,3],
            [1,2,3],
            [1,2,3],
            [1,2,3]
        ];

        $this->hh->addHead($model, $totim, 0, $headData);
    }
}
