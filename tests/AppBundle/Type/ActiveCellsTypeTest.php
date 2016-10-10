<?php

namespace Tests\AppBundle\Type;

use AppBundle\Model\ActiveCells;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActiveCellsTypeTest extends WebTestCase
{

    /** @var Connection */
    protected $dbalConnection;

    /** @var  ActiveCells */
    protected $activeCells;

    public function setUp()
    {
        self::bootKernel();

        $this->dbalConnection = static::$kernel->getContainer()
            ->get('doctrine.dbal.default_connection');

        $this->activeCells = ActiveCells::fromArray(array(
            array(2,3),
            array(1,2)
            )
        );
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'active_cells'));
    }

    public function testConvertToDatabase() {
        $this->assertEquals("a:2:{i:0;a:2:{i:0;b:1;i:1;b:1;}i:1;a:2:{i:0;b:1;i:1;b:1;}}", $this->dbalConnection->convertToDatabaseValue($this->activeCells, 'active_cells'));
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToPHPValue(null, 'active_cells'));
    }

    public function testConvertToPhpValue() {
        $this->assertEquals($this->activeCells, $this->dbalConnection->convertToPHPValue("a:2:{i:0;a:2:{i:0;i:2;i:1;i:3;}i:1;a:2:{i:0;i:1;i:1;i:2;}}", 'active_cells'));
    }

}