<?php

namespace Tests\AppBundle\Type;

use AppBundle\Model\StressPeriodFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WelStressPeriodsTypeTest extends WebTestCase
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

        $wel = StressPeriodFactory::createWel()
            ->setDateTimeBegin(new \DateTime('13.1.2015'))
            ->setDateTimeEnd(new \DateTime('16.1.2015'))
            ->setFlux(1000);

        $this->stressPeriods->add($wel);

        $wel->setFlux(2000);
        $this->stressPeriods->add($wel);
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'wel_stress_periods'));
    }

    public function testConvertToDatabaseAndRevertBack()
    {
        $converted = $this->dbalConnection->convertToDatabaseValue($this->stressPeriods, 'wel_stress_periods');
        $revert = $this->dbalConnection->convertToPHPValue($converted, 'wel_stress_periods');

        $this->assertNotEquals($converted, $revert);
        $this->assertCount(2, $revert);
        $this->assertEquals($this->stressPeriods, $revert);
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToPHPValue(null, 'wel_stress_periods'));
    }
}