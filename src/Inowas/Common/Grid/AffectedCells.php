<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

/**
 * Represents cells with values
 */
class AffectedCells
{
    /**
     * @var array
     * Structure:
     * [
     *  [x, y, value (optional, default: 1)]
     * ]
     */
    private $cells;

    public static function create(): AffectedCells
    {
        return self::fromCells([]);
    }

    public static function fromArray(array $cells): AffectedCells
    {
        return self::fromCells($cells);
    }

    public static function fromCells(array $cells): AffectedCells
    {
        return new self($cells);
    }

    private function __construct(array $cells)
    {
        $this->cells = $cells;
    }

    public function cells(): array
    {
        return $this->cells;
    }

    public function toArray(): array
    {
        return $this->cells();
    }

    public function isEmpty(): bool
    {
        return \count($this->cells()) === 0;
    }
}
