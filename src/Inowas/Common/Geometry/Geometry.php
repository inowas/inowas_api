<?php

declare(strict_types=1);

namespace Inowas\Common\Geometry;

class Geometry implements \JsonSerializable
{
    /** @var  AbstractGeometry */
    private $geometry;

    public static function fromJson(string $json): Geometry
    {
        /*
         * {"type":"Point","coordinates":[105.86406114811,20.963857515931]}
         * {"type":"LineString","coordinates":[[105.78304910628,21.093961475741],[105.79076773351,21.094425931588]]}"
         * {"type":"Polygon","coordinates":[[[-63.65,-31.31],[-63.65,-31.36],[-63.58,-31.36],[-63.58,-31.31],[-63.65,-31.31]]]}"
         */
        $obj = json_decode($json);
        $type = strtolower($obj->type);

        if ($type == 'point') {
            return Geometry::fromPoint(new Point($obj->coordinates[0], $obj->coordinates[1]));
        }

        if ($type == 'linestring') {
            return Geometry::fromLineString(new LineString($obj->coordinates));
        }

        if ($type == 'polygon') {
            return Geometry::fromPolygon(new Polygon($obj->coordinates));
        }

        return null;
    }

    public static function fromPolygon(Polygon $polygon): Geometry
    {
        $self = new self();
        $self->geometry = $polygon;
        return $self;
    }

    public static function fromLineString(LineString $lineString): Geometry
    {
        $self = new self();
        $self->geometry = $lineString;
        return $self;
    }

    public static function fromArray(array $arr): Geometry
    {
        $type = strtolower($arr['type']);
        $coordinates = $arr['coordinates'];
        $srid = $arr['srid'];

        if ($type == 'point') {
            return Geometry::fromPoint(new Point($coordinates, $srid));
        }

        if ($type == 'linestring') {
            return Geometry::fromLineString(new LineString($coordinates, $srid));
        }

        if ($type == 'polygon') {
            return Geometry::fromPolygon(new Polygon($coordinates, $srid));
        }

        return null;
    }

    public static function fromPoint(Point $point): Geometry
    {
        $self = new self();
        $self->geometry = $point;
        return $self;
    }

    public function toArray()
    {
        return [
            'type' => $this->geometry->getType(),
            'coordinates' => $this->geometry->toArray(),
            'srid' => $this->geometry->getSrid()
        ];
    }

    public function toJson()
    {
        return $this->geometry->toJson();
    }

    public function srid(): Srid
    {
         return Srid::fromInt($this->geometry->getSrid());
    }

    public function value()
    {
        return $this->geometry;
    }

    public function jsonSerialize()
    {
        return [
            'type' => $this->geometry->getType(),
            'coordinates' => $this->geometry->toArray(),
            'srid' => $this->geometry->getSrid()
        ];
    }
}
