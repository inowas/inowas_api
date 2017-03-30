<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class Storage
{

    /** @var  SpecificStorage */
    protected $ss;

    /** @var  SpecificYield */
    protected $sy;

    public static function fromParams(SpecificStorage $ss, SpecificYield $sy): Storage
    {
        return new self($ss, $sy);
    }

    private function __construct(SpecificStorage $ss, SpecificYield $sy)
    {
        $this->ss = $ss;
        $this->sy = $sy;
    }

    public function toArray(): array
    {
        return array(
            'ss' => $this->ss->toArray(),
            'sy' => $this->sy->toArray()
        );
    }

    public static function fromArray(array $data): Storage
    {
        $ss = SpecificStorage::fromArray($data['ss']);
        $sy = SpecificYield::fromArray($data['sy']);
        return new self($ss, $sy);
    }

    public function ss(): SpecificStorage
    {
        return $this->ss;
    }

    public function sy(): SpecificYield
    {
        return $this->sy;
    }
}
