<?php

namespace Tests\AppBundle\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RivStressPeriod;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RivStressPeriodsTypeTest extends WebTestCase
{

    /** @var Connection */
    protected $dbalConnection;

    /** @var ArrayCollection */
    protected $stressPeriods;

    public function setUp()
    {
        self::bootKernel();

        $this->dbalConnection = static::$kernel->getContainer()
            ->get('doctrine.dbal.default_connection');

        $this->stressPeriods = new ArrayCollection();

        $riv = new RivStressPeriod(new \DateTime('13.1.2015'), new \DateTime('16.1.2015'), 1.1, true, 111.1);
        $riv->setStage(1.1);
        $riv->setCond(1.1);
        $riv->setRbot(1.1);

        $this->stressPeriods->add($riv);

        $riv->setRbot(2.1);
        $this->stressPeriods->add($riv);
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'riv_stress_periods'));
    }

    public function testConvertToDatabaseAndRevertBack()
    {
        $converted = $this->dbalConnection->convertToDatabaseValue($this->stressPeriods, 'riv_stress_periods');
        $revert = $this->dbalConnection->convertToPHPValue($converted, 'riv_stress_periods');

        $this->assertNotEquals($converted, $revert);
        $this->assertCount(2, $revert);
        $this->assertEquals($this->stressPeriods, $revert);
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToPHPValue(null, 'riv_stress_periods'));
    }
}