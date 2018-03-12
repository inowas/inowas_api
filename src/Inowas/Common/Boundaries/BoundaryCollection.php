<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class BoundaryCollection
{

    /** @var  array */
    private $boundaries = [];

    public static function create(): BoundaryCollection
    {
        return new self();
    }

    private function __construct()
    {
    }

    public function addBoundary(ModflowBoundary $boundary): void
    {
        $this->boundaries[] = $boundary;
    }

    public function toArray(): array
    {
        $boundaries = [];

        /** @var ModflowBoundary $boundary */
        foreach ($this->boundaries as $boundary) {
            $boundaries[] = $boundary->toArray();
        }
        return $boundaries;
    }

    public function filter(BoundaryType $type): BoundaryCollection
    {

        $boundaries = [];

        /** @var ModflowBoundary $boundary */
        foreach ($this->boundaries as $boundary) {
            if ($boundary->type()->sameAs($type)) {
                $boundaries[] = $boundary;
            }
        }

        $this->boundaries = $boundaries;
        return $this;
    }
}
