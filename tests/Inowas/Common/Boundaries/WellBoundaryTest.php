<?php

declare(strict_types=1);

namespace Tests\Inowas\Common\Boundaries;

use Inowas\Common\Boundaries\BoundaryFactory;
use Inowas\Common\Boundaries\Metadata;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Grid\AffectedCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Modflow\Name;

class WellBoundaryTest extends \PHPUnit_Framework_TestCase
{

    /** @var  WellBoundary */
    protected $wellBoundary;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        /** @var WellBoundary $wb */
        $wb = WellBoundary::createWithParams(
            Name::fromString('WellName'),
            Geometry::fromPoint(new Point(10, 12)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()->addWellType(WellType::fromString(WellType::TYPE_PUBLIC_WELL))
        );

        $wb = $wb->addPumpingRate(WellDateTimeValue::fromParams(
            DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')),
            1100
        ));

        $wb = $wb->addPumpingRate(WellDateTimeValue::fromParams(
            DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-02-01')),
            1200
        ));

        $wb = $wb->addPumpingRate(WellDateTimeValue::fromParams(
            DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-03-01')),
            1300
        ));

        $wb = $wb->addPumpingRate(WellDateTimeValue::fromParams(
            DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-04-01')),
            1400
        ));

        $this->wellBoundary = $wb;
    }

    /**
     * @throws \Exception
     */
    public function test_find_value_by_date_time(): void
    {
        $dateTime = DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2014-01-15'));
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(0, $value->pumpingRate());

        $dateTime = DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-15'));
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1100, $value->pumpingRate());

        $dateTime = DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-02-15'));
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1200, $value->pumpingRate());

        $dateTime = DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-03-15'));
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1300, $value->pumpingRate());

        $dateTime = DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-04-15'));
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1400, $value->pumpingRate());

        $dateTime = DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-05-15'));
        $value = $this->wellBoundary->findValueByDateTime($dateTime);
        $this->assertInstanceOf(WellDateTimeValue::class, $value);
        $this->assertEquals(1400, $value->pumpingRate());
    }

    public function test_to_array_from_array(): void
    {
        $arr = $this->wellBoundary->toArray();
        $wb = BoundaryFactory::createFromArray($arr);
        $this->assertInstanceOf(WellBoundary::class, $wb);
        $this->assertEquals($this->wellBoundary, $wb);
    }

    /**
     * @throws \Exception
     */
    public function test_from_json_array(): void
    {
        $expectedWellJson = '{"id":"ca9c62aa-587e-476a-a207-aea7f75da9c2","name":"Well 1","geometry":{"type":"Point","coordinates":[-63.643112,-31.336484]},"type":"wel","affected_layers":[0],"metadata":{"well_type":"puw"},"date_time_values":[{"date_time":"2010-01-01T00:00:00.000Z","values":[0]}],"active_cells":[[28,16]]}';
        $wellArray = json_decode($expectedWellJson, true);
        $wellBoundary = WellBoundary::fromArray($wellArray);
        $this->assertEquals($wellArray['id'], $wellBoundary->boundaryId()->toString());
    }
}
