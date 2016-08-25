<?php

namespace Tests\AppBundle\Type;

use AppBundle\Model\StressPeriodFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RchStressPeriodsTypeTest extends WebTestCase
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

        $rch = StressPeriodFactory::createRch()
            ->setDateTimeBegin(new \DateTime('13.1.2015'))
            ->setDateTimeEnd(new \DateTime('16.1.2015'))
            ->setRech(Flopy2DArray::fromValue(1.5e-3));

        $this->stressPeriods->add($rch);

        $rch->setRech(Flopy2DArray::fromValue(2e-3));
        $this->stressPeriods->add($rch);
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'rch_stress_periods'));
    }

    public function testConvertToDatabaseAndRevertBack()
    {
        $converted = $this->dbalConnection->convertToDatabaseValue($this->stressPeriods, 'rch_stress_periods');
        $revert = $this->dbalConnection->convertToPHPValue($converted, 'rch_stress_periods');

        $this->assertNotEquals($converted, $revert);
        $this->assertCount(2, $revert);
        $this->assertEquals($this->stressPeriods, $revert);
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToPHPValue(null, 'rch_stress_periods'));
    }
}