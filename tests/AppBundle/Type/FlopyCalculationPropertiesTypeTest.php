<?php

namespace Tests\AppBundle\Type;

use AppBundle\Model\ModFlowModelFactory;
use Doctrine\DBAL\Connection;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationPropertiesFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FlopyCalculationPropertiesTypeTest extends WebTestCase
{

    /** @var Connection */
    protected $dbalConnection;

    /** @var  FlopyCalculationProperties */
    protected $calculationProperties;

    public function setUp()
    {
        self::bootKernel();

        $this->dbalConnection = static::$kernel->getContainer()
            ->get('doctrine.dbal.default_connection');

        $this->calculationProperties = FlopyCalculationPropertiesFactory::loadFromApiAndRun(ModFlowModelFactory::create());
        ;
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'flopy_calculation_properties'));
    }

    public function testConvertToDatabase() {
        $this->assertEquals('a:8:{s:9:"load_from";s:3:"api";s:8:"packages";a:6:{i:0;s:2:"mf";i:1;s:3:"dis";i:2;s:3:"bas";i:3;s:3:"lpf";i:4;s:3:"pcg";i:5;s:2:"oc";}s:14:"initial_values";s:3:"ssc";s:5:"check";b:0;s:11:"write_input";b:1;s:3:"run";b:1;s:6:"submit";b:0;s:5:"totim";d:0;}', $this->dbalConnection->convertToDatabaseValue($this->calculationProperties, 'flopy_calculation_properties'));
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToPHPValue(null, 'active_cells'));
    }

    public function testConvertToPhpValue() {
        $this->assertEquals($this->calculationProperties, $this->dbalConnection->convertToPHPValue('a:8:{s:9:"load_from";s:3:"api";s:8:"packages";a:6:{i:0;s:2:"mf";i:1;s:3:"dis";i:2;s:3:"bas";i:3;s:3:"lpf";i:4;s:3:"pcg";i:5;s:2:"oc";}s:14:"initial_values";s:3:"ssc";s:5:"check";b:0;s:11:"write_input";b:1;s:3:"run";b:1;s:6:"submit";b:0;s:5:"totim";d:0;}', 'flopy_calculation_properties'));
    }
}
