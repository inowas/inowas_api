<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\InvalidTypeException;

final class BoundaryType
{
    public const CONSTANT_HEAD = 'chd';
    public const GENERAL_HEAD = 'ghb';
    public const RECHARGE = 'rch';
    public const RIVER = 'riv';
    public const WELL = 'wel';
    public const HEADOBSERVATION = 'hob';

    public static $available = [
        self::CONSTANT_HEAD,
        self::GENERAL_HEAD,
        self::RECHARGE,
        self::RIVER,
        self::WELL,
        self::HEADOBSERVATION
    ];

    /** @var  string */
    private $type;

    /**
     * @param string $type
     * @return BoundaryType
     * @throws \Inowas\Common\Exception\InvalidTypeException
     */
    public static function fromString(string $type): BoundaryType
    {
        if (! \in_array($type, self::$available, true)){
            throw InvalidTypeException::withMessage(sprintf('BoundaryType %s is a not known. Available types are: %s', $type, implode(', ', self::$available)));
        }

        return new self($type);
    }

    /**
     * BoundaryType constructor.
     * @param string $type
     * @throws \Inowas\Common\Exception\InvalidTypeException
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public function toString(): string
    {
        return $this->type;
    }

    public function sameAs($type): bool
    {
        if ($type instanceof self){
            return $this->toString() === $type->toString();
        }

        return false;
    }
}
