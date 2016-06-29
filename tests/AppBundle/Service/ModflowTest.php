<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\Modflow;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModflowTest extends WebTestCase
{
    /** @var Modflow $modflow */
    protected $modflow;

    public function setUp()
    {
        self::bootKernel();

        $this->modflow = static::$kernel->getContainer()
            ->get('inowas.modflow')
        ;
    }

    public function testIsDefaultDataDirectorySet()
    {
        $this->assertTrue(count($this->modflow->getDataFolder())>0);
        $this->assertContains('/app/../var/tmp', $this->modflow->getTmpFolder());
        $this->assertContains('/app/../var/data/modflow/123', $this->modflow->getWorkSpace('123'));
        $this->assertContains('/app/../py/pyprocessing/modflow', $this->modflow->getWorkingDirectory());
        $this->assertContains('/123', $this->modflow->getWorkSpace('123'));
    }

    public function testGetBaseUrlInTestMode()
    {
        $this->assertContains('http://localhost/', $this->modflow->getBaseUrl());
    }
}