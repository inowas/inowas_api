<?php

namespace AppBundle\Tests\Type;

use AppBundle\Model\Interpolation\BoundingBox;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoundingBoxTest extends WebTestCase
{

    /** @var Connection */
    protected $dbalConnection;

    /** @var  BoundingBox */
    protected $boundingBox;

    public function setUp()
    {
        self::bootKernel();

        $this->dbalConnection = static::$kernel->getContainer()
            ->get('doctrine.dbal.default_connection');

        /** @var BoundingBox boundingBox */
        $this->boundingBox = new BoundingBox(578205, 594692, 2316000, 2333500, 32648);
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'bounding_box'));
    }

    public function testConvertToDatabase() {
        $this->assertEquals('{"x_min":578205,"x_max":594692,"y_min":2316000,"y_max":2333500,"srid":32648}', $this->dbalConnection->convertToDatabaseValue($this->boundingBox, 'bounding_box'));
    }

    public function testConvertToPhpValue() {
        $this->assertEquals($this->boundingBox, $this->dbalConnection->convertToPHPValue('{"x_min":578205,"x_max":594692,"y_min":2316000,"y_max":2333500,"srid":32648}', 'bounding_box'));
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(array(), $this->dbalConnection->convertToPHPValue(null, 'bounding_box'));
    }
}