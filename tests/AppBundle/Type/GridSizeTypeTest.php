<?php

namespace AppBundle\Tests\Type;

use AppBundle\Model\Interpolation\GridSize;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GridSizeTypeTest extends WebTestCase
{

    /** @var Connection */
    protected $dbalConnection;

    /** @var  GridSize */
    protected $gridSize;

    public function setUp()
    {
        self::bootKernel();

        $this->dbalConnection = static::$kernel->getContainer()
            ->get('doctrine.dbal.default_connection');

        $this->gridSize = new GridSize(12, 13);
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'grid_size'));
    }

    public function testConvertToDatabase() {
        $this->assertEquals('{"n_x":12,"n_y":13}', $this->dbalConnection->convertToDatabaseValue($this->gridSize, 'grid_size'));
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToPHPValue(null, 'grid_size'));
    }

    public function testConvertToPhpValue() {
        $this->assertEquals($this->gridSize, $this->dbalConnection->convertToPHPValue('{"n_x":12,"n_y":13}', 'grid_size'));
    }

}