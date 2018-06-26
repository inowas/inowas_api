<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

/**
 * Class PackageFlows
 * @package Inowas\Common\Modflow
 *
 * package_flows : {'sfr', 'lak', 'uzf'}
 * Specifies which package flows should be added to the flow-transport
 * link (FTL) file. These values can quickly raise the file size, and
 * therefore the user must request there addition to the FTL file.
 *
 * Default is not to add these terms to the FTL file by omitting the
 * keyword package_flows from the LMT input file.
 */
class PackageFlows
{

    /** @var array $packageFlows */
    protected $packageFlows = [];

    /**
     * @param array $packageFlows
     * @return PackageFlows
     */
    public static function fromArray(array $packageFlows): PackageFlows
    {
        $self = new self();
        $self->packageFlows = $packageFlows;
        return $self;
    }

    /**
     * PackageFlows constructor.
     */
    private function __construct(){}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->packageFlows;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function sameAs($obj): bool
    {
        return $obj instanceof self && $obj->toArray() === $this->packageFlows;
    }
}
