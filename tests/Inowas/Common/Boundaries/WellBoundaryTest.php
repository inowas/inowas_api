<?php

declare(strict_types=1);

namespace Tests\Inowas\Common\Boundaries;

use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Id\BoundaryId;

class WellBoundaryTest extends \PHPUnit_Framework_TestCase
{

    /** @var  WellBoundary */
    protected $wellBoundary;

    public function setUp(): void
    {
        $boundaryId = BoundaryId::generate();

        /** @var WellBoundary $wb */
        $wb = WellBoundary::create($boundaryId);
        $wb = $wb->addPumpingRate(WellDateTimeValue::fromParams(
            new \DateTimeImmutable('2015-01-01'),
            1100
        ));

        $wb = $wb->addPumpingRate(WellDateTimeValue::fromParams(
            new \DateTimeImmutable('2015-02-01'),
            1200
        ));

        $wb = $wb->addPumpingRate(WellDateTimeValue::fromParams(
            new \DateTimeImmutable('2015-03-01'),
            1300
        ));

        $wb = $wb->addPumpingRate(WellDateTimeValue::fromParams(
            new \DateTimeImmutable('2015-04-01'),
            1400
        ));

        $this->wellBoundary = $wb;
    }

    public function test_find_value_by_date_time(): void
    {
        $dateTime = new \DateTimeImmutable('2014-01-15');
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(0, $value->pumpingRate());

        $dateTime = new \DateTimeImmutable('2015-01-15');
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1100, $value->pumpingRate());

        $dateTime = new \DateTimeImmutable('2015-02-15');
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1200, $value->pumpingRate());

        $dateTime = new \DateTimeImmutable('2015-03-15');
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1300, $value->pumpingRate());

        $dateTime = new \DateTimeImmutable('2015-04-15');
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1400, $value->pumpingRate());

        $dateTime = new \DateTimeImmutable('2015-05-15');
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1400, $value->pumpingRate());
    }
}
